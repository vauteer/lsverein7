<?php

namespace App\Pdf;

class SepaPdf extends BasePdf
{
    private $payments;
    private $description;
    private $clubName;
    private $even;
    private $total;
    private $currency;

    function Header()
    {
        $cellHeight = 7;

        $this->SetFont('Arial', 'I', 14);
        $this->Cell(0, 7, $this->clubName, 0, 1, 'C');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, $cellHeight, $this->description, 0, 1);
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(40, $cellHeight, 'Kontoinhaber');
        $this->Cell(20, $cellHeight, 'Mandat');
        $this->Cell(20, $cellHeight, 'Datum');
        $this->Cell(80, $cellHeight, 'Zweck');
        $this->Cell(28, $cellHeight, 'Betrag', 0, 1, 'R');

        $this->SetDrawColor(150, 150, 150);
        $tmp = $this->GetY();
        $this->Line(10, $tmp, 200, $tmp);
        $this->SetY($this->GetY() + 0.2);
    }

    function Footer()
    {
        $this->SetY(-15);
        $tmp = $this->GetY();
        $this->Line(10, $tmp, 200, $tmp);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 7, $this->total . $this->currency, 0, 1);
        $this->Cell(0, 7, 'LS-Verein ' . date('d.m.Y H:i') . ' Seite ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function printEntities()
    {
        $this->SetDrawColor(150, 150, 150);
        $this->SetFillColor(240, 240, 240);
        $cellHeight = 7;
        $this->even = false;
        foreach ($this->payments as $payment)
        {
            $this->even = !$this->even;
            if ($this->even)
            {
                $tmp = $this->GetY() + 0.2;
                $this->Rect(10, $tmp, 190, $cellHeight - 0.2, 'F');
            }
            $this->ClippedCell(40, $cellHeight, mb_convert_encoding($payment['nm'], 'ISO-8859-1', 'UTF-8'));
            $this->ClippedCell(20, $cellHeight, $payment['mndtId']);
            $dateOfSignature = formatDate($payment['dtOfSgntr']);
            $this->Cell(20, $cellHeight, $dateOfSignature);
            $this->ClippedCell(80, $cellHeight, mb_convert_encoding($payment['ustrd'], 'ISO-8859-1', 'UTF-8'));
            $this->ClippedCell(28, $cellHeight, $payment['instdAmt'] . $this->currency, 0, 1, 'R');

            $tmp = $this->GetY();
            $this->Line(10, $tmp, 200, $tmp);
            //$this->SetY($tmp + 0.5);

            $this->total += $payment['amount'];
        }
    }

    public function getOutput($payments, $description, $clubName)
    {
        $this->payments = $payments;
        $this->description = $description;
        $this->clubName = $clubName;
        $this->total = 0;
        $this->currency = ' EUR';

        $this->AliasNbPages();
        $this->AddPage();
        $this->SetFont('Arial', '', 9);

        $this->printEntities();

        return $this->Output('SEPA-Einzug', 'S');
    }

}
