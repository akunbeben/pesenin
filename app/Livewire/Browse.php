<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Scan;
use App\Models\Table;
use App\Support\Encoder;
use App\Traits\Fingerprint;
use App\Traits\Orders\Status;
use Detection\MobileDetect;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Sqids\Sqids;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;

class Browse extends Component implements HasForms, HasInfolists
{
    use Fingerprint;
    use InteractsWithForms;
    use InteractsWithInfolists;
    use WithPagination;

    #[Url]
    public ?string $search = null;

    #[Url]
    public ?string $tab = null;

    #[Locked]
    public Scan $scan;

    #[Locked]
    public string $scanId;

    #[Locked]
    public string $u;

    #[Locked]
    public bool $isIkiosk = false;

    #[Locked]
    public Table $table;

    #[Locked]
    public array $allowedPayments = ['qris', 'ewallet', 'cash'];

    #[Locked]
    public array $qris = ['QRIS'];

    #[Locked]
    public array $ewallet = ['OVO', 'DANA', 'SHOPEEPAY', 'LINKAJA', 'JENIUSPAY'];

    #[Locked]
    public float $feeQRIS = 0.007;

    #[Locked]
    public float $feeEwallet = 0.04;

    public ?string $paymentMethod = null;

    public ?Product $showed;

    public Collection $cart;

    public ?string $variant = null;

    public function tableInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->table)
            ->schema([
                Section::make(fn (Table $record) => $record->merchant->name)
                    ->description(__('Tap to see more details'))
                    ->columns(['default' => 2])
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Table'))
                            ->hidden(Feature::for($this->table->merchant)->active('feature_ikiosk'))
                            ->columnSpan(Feature::for($this->table->merchant)->active('feature_ikiosk') ? 2 : 1),
                        TextEntry::make('seats')
                            ->hidden(Feature::for($this->table->merchant)->active('feature_ikiosk'))
                            ->translateLabel()
                            ->columnSpan(1),
                        TextEntry::make('merchant.name')
                            ->translateLabel()
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public function updatedTab(): void
    {
        $this->resetPage();

        if (
            $this->table->merchant->categories->filter(
                fn (Category $category) => $category->reverse($this->tab, $this->u)
            )->isNotEmpty()
        ) {
            return;
        }

        $this->tab = null;
    }

    #[On('select-variant')]
    public function selectVariant(string $variant): void
    {
        $this->variant = $variant;
    }

    #[On('show-product')]
    public function showProduct(Product $product): void
    {
        $this->showed = $product;

        $this->dispatch('open-modal', id: 'product-detail');
    }

    #[On('close-modal')]
    public function closeProduct(): void
    {
        $this->showed = null;
        $this->variant = null;
    }

    #[On('add-to-cart')]
    public function addToCart(Product $product): void
    {
        if ($this->cart->where('product_id', $product->getKey())->isEmpty()) {
            $this->cart->push([
                'product_id' => $product->getKey(),
                'snapshot' => array_merge($product->toArray(), ['image' => $product->getFirstMediaUrl('banner')]),
                'variant' => $this->variant,
                'note' => null,
                'amount' => 1,
                'price' => $product->price,
            ]);
        }

        $this->dispatch('close-modal', id: 'product-detail');
    }

    #[On('increase-item')]
    public function increase(Product $product): void
    {
        $this->cart = $this->cart->transform(function ($item) use ($product) {
            if ($item['product_id'] !== $product->getKey()) {
                return $item;
            }

            $item['amount'] += 1;

            return $item;
        })->filter();
    }

    #[On('decrease-item')]
    public function decrease(Product $product): void
    {
        $this->cart = $this->cart->transform(function ($item) use ($product) {
            if ($item['product_id'] !== $product->getKey()) {
                return $item;
            }

            if ($item['amount'] <= 1) {
                return null;
            }

            $item['amount'] -= 1;

            return $item;
        })->filter();
    }

    #[On('view-cart')]
    public function viewCart(): void
    {
        $this->dispatch('open-modal', id: 'my-cart');
    }

    public function mount(string $scanId, Request $request): void
    {
        abort_if(blank($request->u), 404);

        [$id, $isIkiosk] = Encoder::decode(
            $this->u = $request->u,
            $this->scanId = $scanId,
        );

        $this->isIkiosk = (bool) $isIkiosk;

        $this->scan = Scan::query()->findOrFail($id);

        $this->table = $this->scan->table;

        if ($this->scan->order && $this->scan->order->status === Status::Pending) {
            $this->redirect($this->getInvoice($this->scan->order->number));

            return;
        }

        if ($this->scan->finished) {
            $this->redirectRoute('summary', [$this->scan->order->number]);

            return;
        }

        abort_if($this->scan->fingerprint !== $this->fingerprint(), 403, 'Invalid request');
        abort_if(Feature::for($this->table->merchant)->active('feature_ikiosk') && ! $this->isIkiosk, 404);
        abort_if($this->scan->created_at->diffInHours() > 1, 403, 'Please rescan the QRCode');
        abort_if(! $this->scan->table, 403, 'Please rescan the QRCode');

        $this->cart = collect([]);
    }

    #[On('pay-now')]
    public function payNow(): void
    {
        if (! in_array($this->paymentMethod, $this->allowedPayments)) {
            Notification::make()
                ->title(__('Invalid payment method'))
                ->body(__('Please choose valid payment method'))
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $additional = [];

        try {
            /** @var \App\Models\Order $order */
            $order = DB::transaction(function () use (&$additional) {
                $subTotal = $this->cart->sum(fn ($item) => $item['price'] * $item['amount']);

                if (Feature::for($this->table->merchant)->active('feature_tax')) {
                    $additional[] = ['type' => 'tax', 'value' => $subTotal * 0.11];
                }

                if (Feature::for($this->table->merchant)->active('feature_fee') && in_array($this->paymentMethod, ['qris', 'ewallet'])) {
                    $additional[] = [
                        'type' => 'fee',
                        'value' => $subTotal * match ($this->paymentMethod) {
                            'ewallet' => $this->feeEwallet,
                            default => $this->feeQRIS,
                        },
                    ];
                }

                $order = Order::query()->create([
                    'status' => match ($this->paymentMethod) {
                        'cash' => Status::Manual,
                        default => Status::Pending,
                    },
                    'scan_id' => $this->scan->getKey(),
                    'total' => $subTotal + array_sum(array_column($additional, 'value')),
                    'additional' => $additional,
                ]);

                $order->items()->createMany($this->cart->toArray());

                $this->scan->update(['finished' => true]);

                return $order;
            });
        } catch (\Throwable $th) {
            logger(null)->error($th->getMessage());

            Notification::make()
                ->title(__('Order failed, please try again.'))
                ->danger()
                ->send();

            return;
        }

        if ($this->paymentMethod === 'cash') {
            $order->payment()->create([
                'merchant_id' => $this->table->merchant_id,
                'business_id' => $this->table->merchant->business_id,
                'event' => 'invoice.manual',
                'data' => [
                    'external_id' => $order->number,
                    'payment_channel' => 'CASH',
                    'paid_amount' => $order->total,
                    'status' => 'PENDING',
                    'paid_at' => now(),
                ],
            ]);
        }

        $url = match ($this->paymentMethod) {
            'cash' => route('summary', [$order]),
            default => $this->processPayment([
                'external_id' => $order->number,
                'description' => "Pesenin {$order->number}",
                'amount' => $order->total,
                'invoice_duration' => 1800, // 30 minutes
                'locale' => 'id',
                'currency' => 'IDR',
                'success_redirect_url' => route('summary', [$order]),
                'failure_redirect_url' => route('summary', [$order]),
                'items' => $this->cart->transform(function ($item) {
                    $name = $item['snapshot']['name'];

                    if ($item['variant']) {
                        $name .= " - {$item['variant']}";
                    }

                    return [
                        'name' => $name,
                        'quantity' => $item['amount'],
                        'price' => $item['price'],
                    ];
                })->toArray(),
                'payment_methods' => match ($this->paymentMethod) {
                    'ewallet' => $this->ewallet,
                    default => $this->qris,
                },
                'fees' => $additional,
            ]),
        };

        $this->redirect($url);

        return;

        Notification::make()
            ->title(__('Order success'))
            ->body(__('Your ordered items will be delivered to you as soon as possible'))
            ->success()
            ->persistent()
            ->send();
    }

    public function render()
    {
        return view('components.browse.index', [
            'products' => $this->table->merchant->products()->available()->when($this->tab, function (Builder $query) {
                $categoryHasNoFilter = $this->table->merchant->categories->filter(
                    fn (Category $category) => $category->reverse($this->tab, $this->u)
                )->isEmpty();

                if ($categoryHasNoFilter) {
                    $this->tab = null;

                    return;
                }

                $id = Arr::first((new Sqids($this->u, 5))->decode($this->tab));

                $query->where('category_id', $id);
            })->search($this->search)
                ->orderBy('recommended', 'desc')
                ->orderBy('name', 'asc')
                ->orderBy('created_at', 'desc')
                ->simplePaginate(
                    ($detect = new MobileDetect())->isMobile() ? ($detect->isTablet() ? 12 : 6) : 12
                ),
            'categories' => $this->table->merchant->categories,
        ]);
    }

    private function getInvoice(string $id): string
    {
        Configuration::setXenditKey(config('services.xendit.secret_key'));

        $api = new InvoiceApi();

        try {
            /** @var \Xendit\Invoice\Invoice $invoice */
            $invoice = collect($api->getInvoices($this->table->merchant->business_id, $id))->first();

            return $invoice->getInvoiceUrl();
        } catch (\Xendit\XenditSdkException $th) {
            logger(null)->error($th->getMessage());
        }
    }

    private function processPayment(array $data)
    {
        Configuration::setXenditKey(config('services.xendit.secret_key'));

        $api = new InvoiceApi();

        $invoice = new CreateInvoiceRequest($data);

        try {
            return $api->createInvoice($invoice, $this->table->merchant->business_id)->getInvoiceUrl();
        } catch (\Xendit\XenditSdkException $th) {
            logger(null)->error($th->getMessage());
        }
    }
}
