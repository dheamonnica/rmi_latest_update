<?php

namespace App\Services;

use FPDF;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

// define('FPDF_FONTPATH','fonts/NotoMono');

class NewPdfInvoice extends FPDF
{
    const ICONV_CHARSET_INPUT = 'UTF-8';
    const ICONV_CHARSET_OUTPUT_A = 'ISO-8859-1//TRANSLIT//IGNORE';
    const ICONV_CHARSET_OUTPUT_B = 'windows-1252//TRANSLIT//IGNORE';

    public $angle = 0;
    public $font = 'helvetica';        /* Font Name : See inc/fpdf/font for all supported fonts */
    public $columnOpacity = 0.06;            /* Items table background color opacity. Range (0.00 - 1) */
    public $columnSpacing = 0.3;                /* Spacing between Item Tables */
    public $referenceformat = ['.', ',', 'left', false];    /* Currency formater */
    public $margins = [
        'l' => 15,
        't' => 15,
        'r' => 15,
    ]; /* l: Left Side , t: Top Side , r: Right Side */
    public $fontSizeProductDescription = 10;                /* font size of product description */

    public $document;
    public $documentOrientation = 'L';
    public $type;
    public $reference;
    public $logo;
    public $systemLogo;
    public $signature;
    public $signatureX;
    public $signatureY;
    public $barcode;
    public $barcodeX;
    public $barcodeY;
    public $barcodeDimensions;
    public $color;
    public $badgeColor;
    public $date;
    public $time;
    public $due;
    public $from;
    public $to;
    public $items;
    public $totals;
    public $badge;
    public $addText;
    public $footernote;
    public $dimensions;
    public $display_tofrom = true;
    protected $displayToFromHeaders = true;
    protected $columns;

    //components
    public $order_status;
    public $po_number_reference;
    public $payment_status;
    public $payment_term;
    public $net_amount;
    public $net_amount_word;
    public $receiver;

    public function __construct($size = 'A4', $currency = '$', $documentOrientation = 'L')
    {
        $this->items = [];
        $this->totals = [];
        $this->addText = [];
        $this->firstColumnWidth = 118;
        $this->currency = $currency;
        $this->maxImageDimensions = [230, 130];
        $this->setDocumentSize($size);
        $this->setDocumentOrientation($documentOrientation);
        $this->setColor('#222222');

        $this->recalculateColumns();

        parent::__construct('L', 'mm', [$this->document['w'], $this->document['h']]);

        $this->AliasNbPages();
        //$this->AddPage();
        $this->SetMargins($this->margins['l'], $this->margins['t'], $this->margins['r']);
    }

    public function setDocumentSize($dsize)
    {
        switch ($dsize) {
            case 'A4':
                $document['w'] = 210;
                $document['h'] = 297;
                break;
            case 'letter':
                $document['w'] = 215.9;
                $document['h'] = 279.4;
                break;
            case 'legal':
                $document['w'] = 215.9;
                $document['h'] = 355.6;
                break;
            default:
                $document['w'] = 210;
                $document['h'] = 297;
                break;
        }

        $this->document = $document;
    }

    private function resizeToFit($image)
    {
        list($width, $height) = getimagesize($image);
        $newWidth = $this->maxImageDimensions[0] / $width;
        $newHeight = $this->maxImageDimensions[1] / $height;
        $scale = min($newWidth, $newHeight);

        return [
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height)),
        ];
    }

    private function pixelsToMM($val)
    {
        $mm_inch = 25.4;
        $dpi = 96;

        return ($val * $mm_inch) / $dpi;
    }

    private function hex2rgb($hex)
    {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [$r, $g, $b];

        return $rgb;
    }

    private function br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }

    public function isValidTimezoneId($zone)
    {
        try {
            new DateTimeZone($zone);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function setTimeZone($zone = '')
    {
        if (!empty($zone) and $this->isValidTimezoneId($zone) === true) {
            date_default_timezone_set($zone);
        }
    }

    public function setDocumentOrientation($orientation)
    {
        $this->documentOrientation = $orientation;
    }

    public function setType($title)
    {
        $this->title = $title;
    }

    public function setColor($rgbcolor)
    {
        $this->color = $this->hex2rgb($rgbcolor);
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setDue($date)
    {
        $this->due = $date;
    }

    public function setLogo($logo = 0, $maxWidth = 0, $maxHeight = 0)
    {
        if ($maxWidth and $maxHeight) {
            $this->maxImageDimensions = [$maxWidth, $maxHeight];
        }
        $this->logo = $logo;
        $this->dimensions = $this->resizeToFit($logo);
    }

    public function setSystemLogo($systemLogo = 0, $maxWidth = 0, $maxHeight = 0)
    {
        if ($maxWidth and $maxHeight) {
            $this->maxImageDimensions = [$maxWidth, $maxHeight];
        }
        $this->systemLogo = $systemLogo;
        $this->dimensions = $this->resizeToFit($systemLogo);
    }

    public function setBarcode($barcode = 0, $maxWidth = 0, $maxHeight = 0, $positionX = 0, $positionY = 0)
    {
        if ($maxWidth and $maxHeight) {
            $this->maxImageDimensions = [$maxWidth, $maxHeight];
        }
        $this->barcode = $barcode;
        $this->barcodeX = $positionX;
        $this->barcodeY = $positionY;
        $this->barcodeDimensions = $this->resizeToFit($barcode);
    }

    public function setSignature($signature = 0, $maxWidth = 0, $maxHeight = 0, $positionX = 0, $positionY = 0)
    {
        if ($maxWidth and $maxHeight) {
            $this->maxImageDimensions = [$maxWidth, $maxHeight];
        }
        $this->signature = $signature;
        $this->signatureX = $positionX;
        $this->signatureY = $positionY;
        $this->dimensions = $this->resizeToFit($signature);
    }

    public function hide_tofrom()
    {
        $this->display_tofrom = false;
    }

    public function hideToFromHeaders()
    {
        $this->displayToFromHeaders = false;
    }

    public function setFrom($data)
    {
        $this->from = $data;
    }

    public function setTo($data)
    {
        $this->to = $data;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function setOrderStatus($order_status)
    {
        $this->order_status = $order_status;
    }

    public function setPoNumberReference($po_number_reference)
    {
        $this->po_number_reference = $po_number_reference;
    }

    public function setPaymentStatus($payment_status)
    {
        $this->payment_status = $payment_status;
    }

    public function setPaymentTerms($payment_term)
    {
        $this->payment_term = $payment_term;
    }

    public function setNetAmountWord($net_amount_word)
    {
        $this->net_amount_word = $net_amount_word;
    }

    public function setNetAmount($net_amount)
    {
        $this->net_amount = $net_amount;
    }

    public function setReceiverName($receiver)
    {
        $this->receiver = $receiver;
    }

    public function setNumberFormat($decimals = '.', $thousands_sep = ',', $alignment = 'left', $space = true)
    {
        $this->referenceformat = [$decimals, $thousands_sep, $alignment, $space];
    }

    public function setFontSizeProductDescription($data)
    {
        $this->fontSizeProductDescription = $data;
    }

    public function flipflop()
    {
        $this->flipflop = true;
    }

    public function price($price)
    {
        $amount = get_formated_decimal($price, false, 2);
        $currency = get_currency_code();
        $space = config('system_settings.show_space_after_symbol') ? ' ' : '';

        if (config('system_settings.currency.symbol_first')) {
            return $currency . $space . $amount;
        }

        return $amount . $space . $currency;
    }

    // public function addItem($item, $description = "", $quantity, $vat, $price, $discount = 0, $total)
    // {
    //     $p['item']        = $item;
    //     $p['description'] = $this->br2nl($description);

    //     if ($vat !== false) {
    //         $p['vat'] = $vat;
    //         if (is_numeric($vat)) {
    //             $p['vat'] = $this->price($vat);
    //         }
    //         $this->vatField = true;
    //         $this->recalculateColumns();
    //     }
    //     $p['quantity'] = $quantity;
    //     $p['price']    = $price;
    //     $p['total']    = $total;

    //     if ($discount !== false) {
    //         $this->firstColumnWidth = 58;
    //         $p['discount']          = $discount;
    //         if (is_numeric($discount)) {
    //             $p['discount'] = $this->price($discount);
    //         }
    //         $this->discountField = true;
    //         $this->recalculateColumns();
    //     }
    //     $this->items[] = $p;
    // }

    public function addItem($item, $description, $quantity, $price)
    {
        $p['item'] = $item;
        $p['description'] = $this->br2nl($description);
        $p['price'] = $price;
        $p['quantity'] = $quantity;
        $p['total'] = $quantity * $price;

        $this->items[] = $p;
    }

    public function addSummary($name, $value = 0, $colored = false)
    {
        $t['name'] = $name;
        $t['value'] = $value;
        if (is_numeric($value)) {
            $t['value'] = $this->price($value);
        }
        $t['colored'] = $colored;
        $this->totals[] = $t;
    }

    public function addTitle($title)
    {
        $this->addText[] = ['title', $title];
    }

    public function addParagraph($paragraph)
    {
        $paragraph = $this->br2nl($paragraph);
        $this->addText[] = ['paragraph', $paragraph];
    }

    public function addBadge($badge, $color = false)
    {
        $this->badge = $badge;

        if ($color) {
            $this->badgeColor = $this->hex2rgb($color);
        } else {
            $this->badgeColor = $this->color;
        }
    }

    public function setFooternote($note)
    {
        $this->footernote = $note;
    }

    public function render($name = '', $destination = '')
    {
        $this->AddPage();
        $this->Body();
        $this->AliasNbPages();

        return $this->Output($destination, $name);
    }

    public function Header()
    {
        if (isset($this->logo) and !empty($this->logo)) {

            $imageType = exif_imagetype($this->logo);

            // $imageContent = file_get_contents($this->logo);

            // if ($imageContent === false) {
            //     echo "Unable to access the image.";
            // } else {
            //     echo "Image accessed successfully.";
            // }

            // echo ini_get('allow_url_fopen');
            //dd($this->logo);
            // dump(IMAGETYPE_WEBP);
            // dd($imageType);

            if ($imageType === IMAGETYPE_WEBP) {
                // Create a new image from the WebP contents
                $image = Image::make(file_get_contents($this->logo));

                // // Convert the image to PNG format
                $image->encode('png');

                // // Generate a unique filename for the PNG image
                $filename = 'logo' . '.png';

                // // Save the PNG image to the public/images directory
                $filePath = 'public/images/' . $filename;

                Storage::put($filePath, $image->getEncoded());

                $this->logo = Storage::url($filePath);

            }

            $this->Image(
                $this->logo,
                $this->margins['l'],
                $this->margins['t'],
                $this->dimensions[0] / 2,
                $this->dimensions[1] / 2
            );
            $this->Ln(1);
        }

        // THIS IS INVOICE PDF

        //Title
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->font, 'B', 20);
        if (isset($this->title) and !empty($this->title)) {
            $this->Cell(0, 5, $this->str_iconv($this->title, true), 0, 1, 'R');
        }
        $this->SetFont($this->font, '', 9);
        $this->Ln(5);

        $lineheight = 5;
        //Calculate position of strings
        $this->SetFont($this->font, 'B', 9);
        $positionX = $this->document['w'] - $this->margins['l'] - $this->margins['r'] - max(
            mb_strtoupper($this->GetStringWidth(trans('invoice.number'), self::ICONV_CHARSET_INPUT)),
            mb_strtoupper($this->GetStringWidth(trans('invoice.date'), self::ICONV_CHARSET_INPUT)),
            mb_strtoupper($this->GetStringWidth(trans('invoice.due'), self::ICONV_CHARSET_INPUT))
        ) - 35;

        //Number
        if (!empty($this->reference)) {
            // $this->Cell($positionX, $lineheight); //whitespace to middle
            //$this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
            //$this->Cell(32, $lineheight, $this->str_iconv(trans('invoice.number'), true) . ':', 0, 0, 'L');
            $width_reference = $this->firstColumnWidth + 100; // Width for the left-aligned label
            $width_value = $this->GetPageWidth() - $width_reference - $this->lMargin - $this->rMargin; // Calculate remaining width for right-aligned value
            $this->SetTextColor(50, 50, 50);
            $this->SetFont($this->font, 'B', 9);

            // Invoice Number
            $this->Cell($width_reference, $lineheight, 'INVOICE NUMBER : ', 0, 0, 'R');
            $this->Cell($width_value, $lineheight, $this->reference, 0, 0, 'R');


            // $this->Cell($width_reference, $lineheight, 'INVOICE NUMBER', 0, 0, 'L');
            // $this->Cell(0, $lineheight, ' : ' .$this->reference, 0, 0, 'R');

            $this->Ln(4);
            $this->Cell($width_reference, $lineheight, 'INVOICE DATE : ', 0, 0, 'R');
            $this->Cell($width_value, $lineheight, $this->date .' '.$this->time , 0, 0, 'R');
            $this->Ln(-4);
        }

        //Due date
        if (!empty($this->due)) {
            $this->Cell($positionX, $lineheight);
            $this->SetFont($this->font, 'B', 9);
            $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
            $this->Cell(32, $lineheight, $this->str_iconv(trans('invoice.due'), true) . ':', 0, 0, 'L');
            $this->SetTextColor(50, 50, 50);
            $this->SetFont($this->font, '', 9);
            $this->Cell(0, $lineheight, $this->due, 0, 1, 'R');
        }

        // First page
        // if ($this->PageNo() == 1) {
            // \Log::info('1111');
            $ymargin = $this->GetY();

            if (isset($this->margins['t']) && isset($this->dimensions[1])) {
                if (($this->margins['t'] + $this->dimensions[1]) > $this->GetY()) {
                    $ymargin = $this->margins['t'] + $this->dimensions[1] + 5;
                }
            }

            $this->SetY($ymargin);

            $this->Ln(-5);
            $this->SetFillColor($this->color[0], $this->color[1], $this->color[2]);
            $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);

            $this->SetDrawColor($this->color[0], $this->color[1], $this->color[2]);
            $this->SetFont($this->font, 'B', 10);
            $width = ($this->document['w'] - $this->margins['l'] - $this->margins['r']) / 2;

            $to_lang = trans('invoice.to');
            $from_lang = trans('invoice.from');

            if (isset($this->flipflop)) {
                $to_lang = trans('invoice.from');
                $from_lang = trans('invoice.to');
                $to = $this->to;
                $from = $this->from;
                $this->to = $from;
                $this->from = $to;
            }

            if ($this->display_tofrom === true) {
                if ($this->displayToFromHeaders === true) {
                    $this->Cell(0, $lineheight, $this->str_iconv($from_lang, true), 0, 1, 'L');
                }

                //Information
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, 'B', 10);
                $this->Cell(0, $lineheight, $this->from[0] ?? '', 0, 0, 'L');
                if($this->order_status) {
                    $this->SetTextColor(50, 50, 50);
                    $this->SetFont($this->font, 'B', 9);
                    $this->Cell(-($width_value), $lineheight, 'ORDER STATUS : ', 0, 0, 'R');
                    $this->Cell(0, $lineheight, $this->str_iconv( $this->order_status, true), 0, 0, 'R', 0);
                }
                $this->Ln();
                $this->SetFont($this->font, '', 8);
                $this->SetTextColor(100, 100, 100);

                for ($i = 1, $iMax = max($this->from === null ? 0 : count($this->from), $this->to === null ? 0 : count($this->to)); $i < $iMax; $i++) {
                    // check if the TO or FROM array value is not empty.
                    $from = isset($this->from[$i]) ? $this->from[$i] : '';

                    $this->Cell(0, $lineheight, $this->str_iconv($from), 0, 0, 'L');
                    // $this->Cell(0, $lineheight, $this->str_iconv($to), 0, 0, 'R');
                    if($i == 1){
                        if($this->po_number_reference)
                        {
                            $this->SetTextColor(50, 50, 50);
                            $this->SetFont($this->font, 'B', 9);
                            $this->Cell(-($width_value), $lineheight, 'PO NUMBER REFERENCE : ', 0, 0, 'R');
                            $this->Cell(0, $lineheight, $this->str_iconv($this->po_number_reference, true), 0, 0, 'R', 0);
                            $this->SetFont($this->font, '', 8);
                            $this->SetTextColor(100, 100, 100);
                        }
                    }

                    if($i == 2){
                        if($this->payment_status)
                        {
                            $this->SetTextColor(50, 50, 50);
                            $this->SetFont($this->font, 'B', 9);
                            $this->Cell(-($width_value), $lineheight, 'PAYMENT STATUS : ', 0, 0, 'R');
                            $this->Cell(0, $lineheight, $this->str_iconv($this->payment_status, true), 0, 0, 'R', 0);
                            $this->SetFont($this->font, '', 8);
                            $this->SetTextColor(100, 100, 100);
                        }
                    }

                    if($i == 3){
                        if($this->payment_term) {
                            $this->SetTextColor(50, 50, 50);
                            $this->SetFont($this->font, 'B', 9);
                            $this->Cell(-($width_value), $lineheight, 'PAYMENT TERM : ', 0, 0, 'R');
                            $this->Cell(0, $lineheight, $this->str_iconv($this->payment_term == 15 ? '< 15 Days' : '40 Days', true), 0, 0, 'R', 0);
                            $this->SetFont($this->font, '', 8);
                            $this->SetTextColor(100, 100, 100);
                        }
                    }

                    // if($i == 3){
                    //     $this->Cell(0, $lineheight, 'LINE 3', 0, 0, 'R');
                    // }
                    $this->Ln();
                }

                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->SetFont($this->font, 'B', 10);
                $this->Cell(0, $lineheight, $this->str_iconv($to_lang, true), 0, 0, 'L');
                // $this->Cell(0, $lineheight, 'LINE another', 0, 0, 'R');
                $this->Ln();

                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, 'B', 10);
                $this->Cell(0, $lineheight, $this->str_iconv($this->to[0] ?? ''), 0, 0, 'L');
                // $this->Cell(0, $lineheight, 'LINE another', 0, 0, 'R');
                $this->Ln();

                $this->SetFont($this->font, '', 8);
                $this->SetTextColor(100, 100, 100);
                for ($i = 1, $iMax = max($this->from === null ? 0 : count($this->from), $this->to === null ? 0 : count($this->to)); $i < $iMax; $i++) {
                    // check if the TO or FROM array value is not empty.
                    $to = isset($this->to[$i]) ? $this->to[$i] : '';

                    // $this->Cell(0, $lineheight, $this->str_iconv($from), 0, 0, 'L');
                    $this->Cell(0, $lineheight, $this->str_iconv($to), 0, 0, 'L');
                    
                    // if($i == 1){
                    //     $this->Cell(0, $lineheight, 'LINE 1', 0, 0, 'R');
                    // }

                    // if($i == 2){
                    //     $this->Cell(0, $lineheight, 'LINE 2', 0, 0, 'R');
                    // }

                    // if($i == 3){
                    //     $this->Cell(0, $lineheight, 'LINE 3', 0, 0, 'R');
                    // }
                    $this->Ln();
                }
                $this->Ln(-6);
                // $this->Ln(5);
            } else {
                $this->Ln(-10);
            }
            
            $this->Ln(2);

        // }
        //Table header
        if (!isset($this->productsEnded)) {
            $width_other = (($this->document['w'] - $this->margins['l'] - $this->margins['r'] - $this->firstColumnWidth - ($this->columns * $this->columnSpacing)) / ($this->columns - 1) + 28);
            $this->SetTextColor(50, 50, 50);
            $this->Ln(5);
            $this->SetFont($this->font, 'B', 9);
            $this->Cell(1, 10, '', 0, 0, 'L', 0);
            $this->Cell(
                $this->firstColumnWidth,
                10,
                $this->str_iconv(trans('invoice.description'), true),
                0,
                0,
                'L',
                0
            );
            // $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            // $this->Cell($width_other - 28, 10, $this->str_iconv(trans('invoice.expired_date'), true), 0, 0, 'C', 0);

            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            $this->Cell($width_other, 10, $this->str_iconv(trans('invoice.qty'), true), 0, 0, 'C', 0);
            if (isset($this->vatField)) {
                $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
                $this->Cell(
                    $width_other,
                    10,
                    $this->str_iconv(trans('invoice.vat'), true),
                    0,
                    0,
                    'C',
                    0
                );
            }
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            $this->Cell($width_other, 10, $this->str_iconv(trans('invoice.price'), true), 0, 0, 'C', 0);
            if (isset($this->discountField)) {
                $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
                $this->Cell(
                    $width_other,
                    10,
                    $this->str_iconv(trans('invoice.discount'), true),
                    0,
                    0,
                    'C',
                    0
                );
            }
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'R', 0);
            $this->Cell($width_other, 10, $this->str_iconv(trans('invoice.total'), true), 0, 0, 'C', 0);
            
            $this->Ln();
            $this->SetLineWidth(0.3);
            $this->SetDrawColor($this->color[0], $this->color[1], $this->color[2]);
            // $this->Line($this->margins['l'], $this->GetY(), $this->margins['r'], $this->GetY());
            $this->Ln(-2);
        } else {
            $this->Ln(1);
        }
    }

    public function Body()
    {
        $width_other = (($this->document['w'] - $this->margins['l'] - $this->margins['r'] - $this->firstColumnWidth - ($this->columns * $this->columnSpacing)) / ($this->columns - 1) + 28);
        $cellHeight = 8;
        $bgcolor = (1 - $this->columnOpacity) * 255;

        if ($this->items) {
            foreach ($this->items as $item) {
                if ((empty($item['item'])) || (empty($item['description']))) {
                    $this->Ln($this->columnSpacing);
                }

                if ($item['description']) {
                    //Precalculate height
                    $calculateHeight = new self;
                    $calculateHeight->addPage();
                    $calculateHeight->setXY(0, 0);
                    $calculateHeight->SetFont($this->font, '', 7);
                    $calculateHeight->MultiCell(
                        $this->firstColumnWidth + 40,
                        3,
                        $this->str_iconv($item['description']),
                        0,
                        'L',
                        1
                    );
                    $descriptionHeight = $calculateHeight->getY() + $cellHeight + 2;
                    $pageHeight = $this->document['h'] - $this->GetY() - $this->margins['t'] - $this->margins['t'];
                    if ($pageHeight < 35) {
                        $this->AddPage();
                    }
                }

                $cHeight = $cellHeight;
                $this->SetFont($this->font, 'b', 8);
                $this->SetTextColor(50, 50, 50);
                $this->SetFillColor($bgcolor, $bgcolor, $bgcolor);
                $this->Cell(1, $cHeight, '', 0, 0, 'L', 1);
                $x = $this->GetX();
                $this->Cell(
                    $this->firstColumnWidth,
                    $cHeight,
                    $this->str_iconv($item['item']),
                    0,
                    0,
                    'L',
                    1
                );

                if ($item['description']) {
                    $resetX = $this->GetX();
                    $resetY = $this->GetY();
                    $this->SetTextColor(120, 120, 120);
                    $this->SetXY($x, $this->GetY() + 8);
                    $this->SetFont($this->font, '', $this->fontSizeProductDescription);
                    $this->MultiCell(
                        $this->firstColumnWidth,
                        floor($this->fontSizeProductDescription / 2),
                        $this->str_iconv($item['description']),
                        0,
                        'L',
                        1
                    );
                    //Calculate Height
                    $newY = $this->GetY();
                    $cHeight = $newY - $resetY + 2;
                    //Make our spacer cell the same height
                    $this->SetXY($x - 1, $resetY);
                    $this->Cell(1, $cHeight, '', 0, 0, 'L', 1);
                    //Draw empty cell
                    $this->SetXY($x, $newY);
                    $this->Cell($this->firstColumnWidth, 2, '', 0, 0, 'L', 1);
                    $this->SetXY($resetX, $resetY);
                }

                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 8);

                // $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                // $this->Cell($width_other, $cHeight, $item['quantity'], 0, 0, 'C', 1);

                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, $item['quantity'], 0, 0, 'C', 1);
                if (isset($this->vatField)) {
                    $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                    if (isset($item['vat'])) {
                        $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $item['vat']), 0, 0, 'C', 1);
                    } else {
                        $this->Cell($width_other, $cHeight, '', 0, 0, 'C', 1);
                    }
                }
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $this->price($item['price'])), 0, 0, 'C', 1);
                if (isset($this->discountField)) {
                    $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                    if (isset($item['discount'])) {
                        $this->Cell(
                            $width_other,
                            $cHeight,
                            iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $item['discount']),
                            0,
                            0,
                            'C',
                            1
                        );
                    } else {
                        $this->Cell($width_other, $cHeight, '', 0, 0, 'C', 1);
                    }
                }
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $this->price($item['total'])), 0, 0, 'C', 1);
                $this->Ln();
                $this->Ln($this->columnSpacing);
            }
        }

        $badgeX = $this->getX();
        $badgeY = $this->getY();

        //Add totals
        if ($this->totals) {
            foreach ($this->totals as $total) {
                $this->SetTextColor(50, 50, 50);
                $this->SetFillColor($bgcolor, $bgcolor, $bgcolor);
                $this->Cell(1 + $this->firstColumnWidth, $cellHeight, '', 0, 0, 'L', 0);
                for ($i = 0; $i < $this->columns - 3; $i++) {
                    $this->Cell($width_other, $cellHeight, '', 0, 0, 'L', 0);
                    $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                }
                $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                if ($total['colored']) {
                    $this->SetTextColor(255, 255, 255);
                    $this->SetFillColor($this->color[0], $this->color[1], $this->color[2]);
                }
                $this->SetFont($this->font, 'b', 8);
                $this->Cell(1, $cellHeight, '', 0, 0, 'L', 1);
                $this->Cell(
                    $width_other - 1,
                    $cellHeight,
                    iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $total['name']),
                    0,
                    0,
                    'L',
                    1
                );
                $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                $this->SetFont($this->font, 'b', 8);
                $this->SetFillColor($bgcolor, $bgcolor, $bgcolor);
                if ($total['colored']) {
                    $this->SetTextColor(255, 255, 255);
                    $this->SetFillColor($this->color[0], $this->color[1], $this->color[2]);
                }
                $this->Cell($width_other, $cellHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $total['value']), 0, 0, 'C', 1);
                $this->Ln();
                $this->Ln($this->columnSpacing);
            }
        }
        $this->productsEnded = true;
        $this->Ln();
        $this->Ln(1);

        //Badge
        if ($this->badge) {
            $badge = ' ' . mb_strtoupper($this->badge, self::ICONV_CHARSET_INPUT) . ' ';
            $resetX = $this->getX();
            $resetY = $this->getY();
            $this->setXY($badgeX, $badgeY + 15);
            $this->SetLineWidth(0.4);
            $this->SetDrawColor($this->badgeColor[0], $this->badgeColor[1], $this->badgeColor[2]);
            $this->setTextColor($this->badgeColor[0], $this->badgeColor[1], $this->badgeColor[2]);
            $this->SetFont($this->font, 'b', 15);
            $this->Rotate(10, $this->getX(), $this->getY());
            $this->Rect($this->GetX(), $this->GetY(), $this->GetStringWidth($badge) + 2, 10);
            $this->Write(10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, mb_strtoupper($badge, self::ICONV_CHARSET_INPUT)));
            $this->Rotate(0);

            if ($resetY > $this->getY() + 5) {
                $this->setXY($resetX, $resetY);
            } else {
                $this->Ln(4);
            }
        }

        $this->Ln(7);
        //payment information
        $this->SetFont($this->font, 'b', 9);
        $this->SetTextColor(50, 50, 50);

        if($this->net_amount)
        {
            $this->Cell(0, 10, $this->str_iconv('Net Amount : '.$this->price($this->net_amount), true), 0, 0, 'L', 0);
            $this->Ln(4);
        }

        if($this->net_amount_word)
        {
            $this->Cell(0, 10, $this->str_iconv('Say : '.$this->net_amount_word, true), 0, 0, 'L', 0);
        }

        // if($this->receiver)
        // {
        //     $this->Cell(0, 10, $this->receiver, 0, 1, 'R', 0);
        // }

        $this->Ln(1);
        //#SingatureO
        if (isset($this->signature) and !empty($this->signature)) {
            $this->Image(
                $this->signature,
                $this->document['h'] - $this->dimensions[0] - $this->margins['r'] - 5,
                $this->getY() - 5,
                $this->dimensions[0],
                $this->dimensions[1]
            );
        }

        $this->Ln(7);

        if($this->receiver)
        {
            $this->Cell(0, 10, 'Received By : '.$this->receiver, 0, 0, 'R', 0);
        }
        $this->Ln(1);

        //Add information
        foreach ($this->addText as $text) {
            if ($text[0] == 'title') {
                $this->SetFont($this->font, 'b', 9);
                $this->SetTextColor(50, 50, 50);
                $this->Cell(0, 10, $this->str_iconv($text[1], true), 0, 0, 'L', 0);
                $this->Ln();
                $this->SetLineWidth(0.3);
                $this->SetDrawColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Line(
                    $this->margins['l'],
                    $this->GetY(),
                    $this->document['w'] - $this->margins['r'],
                    $this->GetY()
                );
                $this->Ln(4);
            }
            if ($text[0] == 'paragraph') {
                $this->SetTextColor(80, 80, 80);
                $this->SetFont($this->font, '', 8);
                $this->MultiCell(0, 3, $this->str_iconv($text[1]), 0, 'L', 0);
                $this->Ln(1);
            }
        }
        $this->Ln(10);

        $barcodeX = $this->getX();
        $barcodeY = $this->getY();

        //#BarcodeO
        if (isset($this->barcode) and !empty($this->barcode)) {
            $this->Image(
                $this->barcode,
                // $this->margins['t'],
                $barcodeX,
                $barcodeY,
                // $this->document['w'] - 25, // set X position
                // $this->getY() - $this->margins['t'], //set Y position
                35,
                10
            );
        }

        $this->Ln(10);
    }

    public function Footer()
    {
        // $this->SetY(-$this->margins['t']);
        $this->SetFont($this->font, '', 8);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 10, $this->footernote, 0, 0, 'L');
        $this->Cell(
            0,
            10,
            iconv('UTF-8', 'ISO-8859-1', trans('invoice.page')) . ' ' . $this->PageNo() . ' ' . trans('invoice.page_of') . ' {nb}',
            0,
            0,
            'R'
        );
    }

    public function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1) {
            $x = $this->x;
        }
        if ($y == -1) {
            $y = $this->y;
        }
        if ($this->angle != 0) {
            $this->_out('Q');
        }
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf(
                'q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',
                $c,
                $s,
                -$s,
                $c,
                $cx,
                $cy,
                -$cx,
                -$cy
            ));
        }
    }

    public function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    private function recalculateColumns()
    {
        $this->columns = 4;

        if (isset($this->vatField)) {
            $this->columns += 1;
        }

        if (isset($this->discountField)) {
            $this->columns += 1;
        }
    }

    private function str_iconv($str = '', $toupper = false)
    {
        $str = $toupper ? mb_strtoupper($str, self::ICONV_CHARSET_INPUT) : $str;

        return iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $str);
    }
}
