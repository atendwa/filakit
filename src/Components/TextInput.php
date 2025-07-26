<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TextInput extends \Filament\Forms\Components\TextInput
{
    public function headline(): TextInput
    {
        return $this->formatStateUsing(fn (string $state): string => headline($state));
    }

    public function date(?string $column = null): TextInput
    {
        return $this->formatStateUsing(function ($record = null) use ($column) {
            if (blank($record)) {
                return null;
            }

            $record = asInstanceOf($record, Model::class);
            $column = $column ?? $this->getName();

            if (method_exists($record, 'date')) {
                $date = $record->date($column);

                if ($date instanceof Carbon) {
                    return $date->format('D, d M Y, H:i:s');
                }

                return null;
            }

            return $record->getAttribute($column);
        });
    }

    public function empty(): TextInput
    {
        return $this->placeholder('N/A');
    }

    public function class(): TextInput
    {
        return $this->formatStateUsing(fn (?string $state) => filled($state) ? class_basename($state) : 'N/A');
    }

    public function relation(string $relation, string $column = 'name'): TextInput
    {
        return $this->label(ucfirst($relation))->formatStateUsing(function (Model $model) use ($column, $relation) {
            $relation = $model->getRelationValue(mb_strtolower($relation));

            if ($relation instanceof Model) {
                return $relation->getAttribute($column);
            }

            return 'N/A';
        });
    }
}
