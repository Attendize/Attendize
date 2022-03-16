<?php


namespace App\Services\PDFGenerator;


class PDFFile
{
    /**
     * @var string $name The file name without extension
     */
    public $name;

    /**
     * @var string $name The file name with extension
     */
    public $basename;

    /**
     * @var string $base_path The path where PDF file is stored
     */
    public $base_path;

    /**
     * @var string $path The full file path
     */
    public $path;

    /**
     * @var bool $error PDF File generation has failed?
     */
    public $error = false;

    /**
     * @var bool $cached The file is served from an existing PDF, it is not newly created
     */
    public $cached = false;

    /**
     * Creates a new PDFFile class
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->basename = $this->name . '.pdf';
        $this->base_path = public_path(config('attendize.event_pdf_tickets_path'));
        $this->path = $this->base_path . DIRECTORY_SEPARATOR . $this->basename;
    }
}
