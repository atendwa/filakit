<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Carbon\CarbonInterface;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Imports\Models\Import;

trait CustomizesFilamentImportExport
{
    public function getJobRetryUntil(): ?CarbonInterface
    {
        return null;
    }

    public static function getCompletedNotificationBody(Export|Import $action): string
    {
        $type = $action instanceof Export ? 'export' : 'import';

        $body = 'Your account request ' . $type . ' has completed and ' .
            number_format($action->successful_rows) . ' ' .
            str('row')->plural($action->successful_rows) . $type . 'ed.';

        $failedRowsCount = $action->getFailedRowsCount();

        if ($failedRowsCount > 0) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' .
                str('row')->plural($failedRowsCount) . ' failed to ' . $type . '.';
        }

        return $body;
    }
}
