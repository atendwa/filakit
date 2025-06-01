<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Providers;

use Atendwa\Filakit\Table\Actions\CreateAction;
use Atendwa\Filakit\Table\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class FilakitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\Filament\Actions\CreateAction::class, \Atendwa\Filakit\Actions\CreateAction::class);
        $this->app->bind(\Filament\Actions\EditAction::class, \Atendwa\Filakit\Actions\EditAction::class);
        $this->app->bind(\Filament\Tables\Actions\CreateAction::class, CreateAction::class);
        $this->app->bind(\Filament\Tables\Actions\EditAction::class, EditAction::class);

        $this->mergeConfigFrom(__DIR__ . '/../../config/filakit.php', 'filakit');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'filakit');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../../database/migrations' => database_path('migrations')], 'migrations');

            $this->publishes([__DIR__ . '/../../config/filakit.php' => config_path('filakit.php')], 'config');
        }

        FilamentIcon::register(['actions::view-action' => 'heroicon-o-arrow-long-right']);

        ViewAction::configureUsing(
            fn (ViewAction $viewAction): ViewAction => $viewAction->button()->iconPosition('after')->slideOver()
        );

        ImportAction::configureUsing(
            fn (ImportAction $importAction): ImportAction => $importAction->chunkSize(100)->color('gray')
                ->slideOver()->maxRows(1000)->icon('heroicon-o-document-text')
        );

        ExportAction::configureUsing(
            fn (ExportAction $exportAction): ExportAction => $exportAction->color('gray')
                ->slideOver()->icon('heroicon-o-document-arrow-down')
        );

        RestoreAction::configureUsing(
            fn (RestoreAction $restoreAction): RestoreAction => $restoreAction->icon('heroicon-o-arrow-path')
        );

        Notification::configureUsing(
            fn (Notification $notification): Notification => $notification->duration(20000)
        );

        KeyValueEntry::configureUsing(
            fn (KeyValueEntry $keyValueEntry): KeyValueEntry => $keyValueEntry->columnSpanFull()
        );

        DeleteAction::configureUsing(
            fn (DeleteAction $deleteAction): DeleteAction => $deleteAction->icon('heroicon-o-trash')
        );

        TextColumn::configureUsing(fn (TextColumn $textColumn): TextColumn => $textColumn->placeholder('N/A'));
        ActionGroup::configureUsing(fn (ActionGroup $actionGroup): ActionGroup => $actionGroup->color('gray'));
        FileUpload::configureUsing(fn (FileUpload $fileUpload): FileUpload => $fileUpload->columnSpanFull());
        Select::configureUsing(fn (Select $select): Select => $select->searchable()->preload()->reactive());

        ForceDeleteAction::configureUsing(
            fn (ForceDeleteAction $forceDeleteAction): ForceDeleteAction => $forceDeleteAction
                ->icon('heroicon-o-exclamation-triangle')
        );

        \Filament\Actions\ViewAction::configureUsing(
            fn (\Filament\Actions\ViewAction $viewAction): \Filament\Actions\ViewAction => $viewAction
                ->icon('heroicon-o-arrow-long-left')
        );

        Textarea::configureUsing(
            fn (Textarea $textarea): Textarea => $textarea->rows(3)->columnSpanFull()->maxLength(255)
        );

        Table::configureUsing(
            fn (Table $table): Table => $table
                ->paginated([10, 25, 50, 100, 'all'])->defaultPaginationPageOption(10)->extremePaginationLinks()
                ->defaultSort('updated_at', 'desc')->deselectAllRecordsWhenFiltered(false)
                ->emptyStateIcon('heroicon-o-inbox')->emptyStateHeading('No records')->striped()
                ->filtersFormWidth(MaxWidth::ThreeExtraLarge)->filtersTriggerAction(
                    fn (Action $action): Action => $action->button()->slideOver()->icon('heroicon-o-funnel')
                )
        );
    }
}
