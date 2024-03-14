<?php

namespace App\Forms\Components;

use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class CustomImage extends SpatieMediaLibraryImageColumn
{
    public function getState(): array
    {
        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $records = Arr::wrap($record);

        $state = [];

        foreach ($records as $record) {
            /** @var Model $record */
            $state = [
                ...$state,
                ...$record->getRelationValue('media')
                    ->sortBy('order_column')
                    ->pluck('uuid')
                    ->all(),
            ];
        }

        if (blank($state)) {
            return [\Illuminate\Support\Str::orderedUuid()->toString()];
        }

        return array_unique($state);
    }

    public function getImageUrl(?string $state = null): ?string
    {
        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $records = Arr::wrap($record);

        foreach ($records as $record) {
            /** @var Model $record */

            /** @var ?Media $media */
            $media = $record->getRelationValue('media')->first(fn (Media $media): bool => $media->uuid === $state);

            if (! $media) {
                return $record->getFallbackMediaUrl($this->getCollection());
            }

            $conversion = $this->getConversion();

            if ($this->getVisibility() === 'private') {
                try {
                    return $media->getTemporaryUrl(
                        now()->addMinutes(5),
                        $conversion ?? '',
                    );
                } catch (Throwable $exception) {
                    // This driver does not support creating temporary URLs.
                }
            }

            return $media->getAvailableUrl(Arr::wrap($conversion));
        }

        return null;
    }
}
