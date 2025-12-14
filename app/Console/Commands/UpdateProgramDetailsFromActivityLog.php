<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- IMPORT THE LOG FACADE

class UpdateProgramDetailsFromActivityLog extends Command
{
    // ... (signature and description remain the same)
    protected $signature = 'data:migrate-program-details';
    protected $description = 'Updates program_details fields (dest_qty, old_qty) based on specific activity_log records.';

    public function handle()
    {
        $this->info('Starting migration for Program Details...');
        
        $programId = 136;
        $destQtyToExclude = '12';
        
        DB::table('activity_log')
            ->orderBy('id') // Mandatory for chunking
            
            // Apply your complex JSON-based WHERE clause
            ->whereJsonContains('properties->attributes->program_id', $programId)
            ->where(function ($query) use ($destQtyToExclude) {
                if (DB::connection()->getPdo()->getAttribute(DB::connection()->getPdo()::ATTR_DRIVER_NAME) === 'mysql') {
                    $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.attributes.dest_qty')) != ?", [$destQtyToExclude])
                          ->whereRaw("JSON_EXTRACT(properties, '$.old.old_qty') IS NULL");
                } elseif (DB::connection()->getPdo()->getAttribute(DB::connection()->getPdo()::ATTR_DRIVER_NAME) === 'pgsql') {
                    $query->whereRaw("(properties->'attributes'->>'dest_qty') != ?", [$destQtyToExclude])
                          ->whereRaw("(properties->'old'->>'old_qty') IS NULL");
                } else {
                    $this->error('Unsupported database driver for complex JSON queries.');
                    return;
                }
            })
            
            ->chunk(100, function ($logs) {
                foreach ($logs as $log) {
                    $properties = json_decode($log->properties, true);

                    if (isset($properties['attributes']['id']) && 
                        isset($properties['attributes']['dest_qty']) &&
                        isset($properties['attributes']['old_qty'])) {
                        
                        // LOGGING SUCCESSFUL UPDATE DATA
                        $programDetailsId = $properties['attributes']['id'];
                        $newDestQty = $properties['attributes']['dest_qty'];
                        $newOldQty = $properties['attributes']['old_qty'];
                        
                        $updated = DB::table('program_details')
                            ->where('id', $programDetailsId)
                            ->update([
                                'dest_qty' => $newDestQty,
                                'old_qty'  => $newOldQty,
                            ]);

                        if ($updated) {
                            $this->line("Updated ID: {$programDetailsId} | dest_qty: {$newDestQty} | old_qty: {$newOldQty}");
                            // Log the success with the ID and the values used
                            Log::info("ProgramDetails Update SUCCESS", [
                                'activity_log_id' => $log->id,
                                'program_details_id' => $programDetailsId,
                                'dest_qty' => $newDestQty,
                                'old_qty' => $newOldQty,
                            ]);
                        } else {
                            $this->warn("Could not find/update program_details ID: {$programDetailsId}");
                            // Log the warning
                            Log::warning("ProgramDetails Update WARNING: Record not found in program_details.", [
                                'activity_log_id' => $log->id,
                                'program_details_id' => $programDetailsId,
                            ]);
                        }
                    } else {
                        // LOGGING FAILED DATA
                        $errorMessage = "Missing required keys for update: activity_log ID: {$log->id}";
                        $this->error($errorMessage);
                        
                        // Log the entire properties JSON for inspection
                        Log::error($errorMessage, [
                            'activity_log_id' => $log->id,
                            'properties_data' => $log->properties, // Log the raw JSON string
                        ]);
                    }
                }
            });

        $this->info('Program Details migration complete. Check laravel.log for missing key details.');
        return Command::SUCCESS;
    }
}