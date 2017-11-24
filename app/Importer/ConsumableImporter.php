<?php

namespace App\Importer;

use App\Helpers\Helper;
use App\Models\Consumable;

class ConsumableImporter extends ItemImporter
{
    public function __construct($filename)
    {
        parent::__construct($filename);
    }

    protected function handle($row)
    {
        parent::handle($row); // TODO: Change the autogenerated stub
        $this->createConsumableIfNotExists();
    }

    /**
     * Create a consumable if a duplicate does not exist
     *
     * @author Daniel Melzter
     * @since 3.0
     */
    public function createConsumableIfNotExists()
    {
        $consumable = Consumable::where('name', $this->item['name'])->first();
        if ($consumable) {
            if (!$this->updating) {
                $this->log('A matching Consumable ' . $this->item["name"] . ' already exists.  ');
                return;
            }
            $this->log('Updating Consumable');
            $consumable->update($this->sanitizeItemForUpdating($consumable));
            $consumable->save();
            return;
        }
        $this->log("No matching consumable, creating one");
        $consumable = new Consumable();
        $consumable->fill($this->sanitizeItemForStoring($consumable));

        if ($consumable->save()) {
            $consumable->logCreate('Imported using CSV Importer');
            $this->log("Consumable " . $this->item["name"] . ' was created');
            return;
        }
        $this->logError($consumable, 'Consumable');
        return;
    }
}
