<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var \App\Models\Merchant $merchant */
        $merchant = User::first()->merchants()->create([
            'name' => 'Coffee Corner',
            'address' => 'Jalan Angkasa No 36',
            'phone' => '089631581118',
            'city' => 'Banjarbaru',
            'country' => 'Indonesia',
            'zip' => '70724',
        ]);

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories */
        $categories = $merchant->categories()->createMany([
            ['name' => 'Espresso'],
            ['name' => 'Latte'],
            ['name' => 'Coffee'],
        ]);

        collect([
            [
                'name' => 'Espresso',
                'description' => 'A strong and concentrated coffee shot, perfect for those who love the pure essence of coffee.',
                'price' => 15000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://majalah.ottenstatic.com/uploads/2016/09/espresso-013-1024x681.jpg',
            ],
            [
                'name' => 'Cappuccino',
                'description' => 'Espresso topped with steamed milk and a generous layer of frothy milk foam, creating a balanced and creamy coffee experience.',
                'price' => 18000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://id.jura.com/-/media/global/images/coffee-recipes/images-redesign-2020/cappuccino_2000x1400px.jpg',
            ],
            [
                'name' => 'Latte',
                'description' => 'Espresso mixed with steamed milk, resulting in a smooth and mellow coffee with a hint of creaminess.',
                'price' => 17000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://www.foodandwine.com/thmb/CCe2JUHfjCQ44L0YTbCu97ukUzA=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/Partners-Latte-FT-BLOG0523-09569880de524fe487831d95184495cc.jpg',
            ],
            [
                'name' => 'Mocha',
                'description' => 'A delightful combination of espresso, steamed milk, and chocolate, creating a rich and slightly sweet coffee drink.',
                'price' => 20000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://images.immediate.co.uk/production/volatile/sites/2/2021/11/Mocha-1fc71f7.png?quality=90&resize=556,505',
            ],
            [
                'name' => 'Macchiato',
                'description' => 'Espresso "stained" with a dollop of frothy milk, offering a bold coffee flavor with a touch of milkiness.',
                'price' => 14000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://kopibandung.com/wp-content/uploads/2022/04/kopi-macchiato-espresso-susu.jpg',
            ],
            [
                'name' => 'Ristretto',
                'description' => 'An even stronger and more concentrated espresso shot than the standard, for those seeking an intense coffee experience.',
                'price' => 16000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://cafe1820.com/wp-content/uploads/2022/03/Cafe-ristretto-1.jpg',
            ],
            [
                'name' => 'Kopi Tubruk',
                'description' => 'Javanese-style coffee made by boiling coffee grounds with a lump of sugar, producing a unique and sweetened coffee flavor.',
                'price' => 13000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://dcostseafood.id/wp-content/uploads/2022/03/Kopi-Tubruk.jpg',
            ],
            [
                'name' => 'Kopi Susu',
                'description' => 'A simple but satisfying coffee made with strong Indonesian coffee and condensed milk for sweetness and creaminess.',
                'price' => 14000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://asset.kompas.com/crops/abzfBx7I1v0rsldNAm3UjFoH63I=/0x0:1000x667/750x500/data/photo/2020/07/26/5f1d9e3132c94.jpg',
            ],
            [
                'name' => 'Aceh Gayo',
                'description' => 'A single-origin coffee from the Aceh region of Indonesia, known for its earthy and herbal notes with a medium body.',
                'price' => 22000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://journal.momotrip.co.id/wp-content/uploads/2017/02/Kopi-Aceh.jpg',
            ],
            [
                'name' => 'Bali Kintamani',
                'description' => 'A coffee from the volcanic soils of Bali, offering bright acidity and citrusy notes with a medium body.',
                'price' => 23000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://www.nescafe.com/id/sites/default/files/Kopi%20Kintamani%20Kopi%20Arabika%20dengan%20Cita%20Rasa%20yang%20Unik.jpg',
            ],
            [
                'name' => 'Sumatra Mandheling',
                'description' => 'A renowned Indonesian coffee with a heavy body and earthy, herbal flavors, often enjoyed as a dark roast.',
                'price' => 21000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://buahberdikari.com/storage/2021/03/sensecuadorcom.jpg',
            ],
            [
                'name' => 'Toraja Sulawesi',
                'description' => 'Coffee from the highlands of Sulawesi, known for its full body, mild acidity, and hints of dark chocolate.',
                'price' => 24000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://awsimages.detik.net.id/community/media/visual/2023/01/18/fakta-biji-kopi-toraja-1.jpeg?w=1200',
            ],
            [
                'name' => 'Java Arabica',
                'description' => 'A classic Indonesian coffee with a medium body, bright acidity, and a well-balanced flavor profile.',
                'price' => 20000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://asset-2.tstatic.net/style/foto/bank/images/kopi_20170822_171550.jpg',
            ],
            [
                'name' => 'Papua Wamena',
                'description' => 'Coffee from Papua, offering a fruity and winey flavor with medium body and a unique Indonesian coffee experience.',
                'price' => 23000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://miro.medium.com/v2/resize:fit:1358/1*u8bOWB9aRLVT9G6Fko0EaQ.jpeg',
            ],
            [
                'name' => 'Kopi Tumpang',
                'description' => 'A local specialty, Kopi Tumpang is made by layering coffee grounds and condensed milk, creating a visually striking and sweet coffee.',
                'price' => 17000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://assets.pikiran-rakyat.com/crop/0x0:0x0/x/photo/2020/11/27/4278595172.jpg',
            ],
            [
                'name' => 'Kopi Jahe',
                'description' => 'Indonesian coffee infused with ginger, offering a warm and spicy twist to your coffee experience.',
                'price' => 16000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://img-global.cpcdn.com/recipes/9466ed9bb1eef620/1200x630cq70/photo.jpg',
            ],
            [
                'name' => 'Kopi Kelapa',
                'description' => 'A unique blend of coffee and coconut milk, creating a creamy and tropical coffee delight.',
                'price' => 19000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://www.taslabnews.com/wp-content/uploads/2019/09/bisnis-es-kopi-1.jpg',
            ],
            [
                'name' => 'Kopi Pandan',
                'description' => 'Coffee infused with pandan leaves, giving it a fragrant and refreshing twist with a hint of sweetness.',
                'price' => 18000,
                'availability' => true,
                'recommended' => rand(0, 1),
                'variants' => $this->variants(),
                'image' => 'https://www.jagel.id/api/listimage/v/Pandan-Coffee-Ice-0-7845f300ffed0164.jpg',
            ],
        ])->each(function ($product) use ($categories, $merchant) {
            $product['category_id'] = $categories->random()->getKey();
            $product['merchant_id'] = $merchant->getKey();
            $image = $product['image'];

            unset($product['image']);

            $product = Product::query()->create($product);

            $product->addMediaFromUrl($image)->toMediaCollection('banner');
        });

        $merchant->tables()->create(['number' => 1, 'seats' => 4]);
    }

    private function variants(): ?array
    {
        $options = ['Regular', 'Large'];

        return (bool) rand(0, 1) ? $options : null;
    }
}
