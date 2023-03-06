<?php

namespace App\Jobs;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportDataToXLS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $path;
    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/doc';
        file_put_contents($this->path, '');
        $writer = WriterEntityFactory::createXLSXWriter();
        $values = array();
        $writer->openToFile($filePath);
        foreach ($this->data as $items) {
            foreach ($items as $item) {
                $values[] = (string)$item;
            }
        }
        $rowFromValues = WriterEntityFactory::createRowFromArray($values);
        $writer->addRow($rowFromValues);
        $writer->close();
    }
}
