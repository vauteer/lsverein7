<?php

namespace App\Pdf;

class MemberRolesPdf extends BasePdf
{
    private $entities;
    private $leftHeadline;
    private $rightHeadline;
    private $clubName;
    private $even;

    function Header()
    {
        $cellHeight = 7;

        $this->SetFont('Arial', 'I', 14);
        $this->Cell(0, 7, $this->clubName, 0, 1, 'C');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(140, $cellHeight, $this->leftHeadline);
        $this->Cell(50, $cellHeight, $this->rightHeadline, 0, 1, "R");
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(8, $cellHeight, '#');
        $this->Cell(40, $cellHeight, 'Name');
        $this->Cell(15, $cellHeight, 'Alter', align: 'C');
        $this->Cell(65, $cellHeight, 'Funktionen');
        $this->Cell(65, $cellHeight, 'Sparten', 0, 1);

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
        $this->Cell(0, 7, 'LS-Verein ' . date('d.m.Y H:i') . ' Seite ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function printEntities()
    {
        $this->SetDrawColor(150, 150, 150);
        $this->SetFillColor(240, 240, 240);
        $cellHeight = 7;
        $this->even = false;
        foreach ($this->entities as $member)
        {
            $this->even = !$this->even;
            if ($this->even)
            {
                $tmp = $this->GetY() + 0.2;
                $this->Rect(10, $tmp, 190, $cellHeight - 0.2, 'F');
            }
            $this->Cell(8, $cellHeight, $member->id, 0, 0, 'R');
            $this->ClippedCell(40, $cellHeight,
                mb_convert_encoding($member->surname . ' ' . $member->first_name, 'ISO-8859-1', 'UTF-8'));
            $this->Cell(15, $cellHeight, $member->age, 0, 0, 'C');
            $this->ClippedCell(65, $cellHeight, mb_convert_encoding($member->currentRoles(), 'ISO-8859-1', 'UTF-8'), 0, 0);
            $this->ClippedCell(65, $cellHeight, mb_convert_encoding($member->currentSections(), 'ISO-8859-1', 'UTF-8'), 0, 1);
            $tmp = $this->GetY();
            $this->Line(10, $tmp, 200, $tmp);
            //$this->SetY($tmp + 0.5);
        }
    }

    public function getOutput($entities, $clubName, $leftHeadline, $rightHeadline)
    {
        $this->entities = $entities;
        $this->leftHeadline = $leftHeadline;
        $this->rightHeadline = $rightHeadline;
        $this->clubName = $clubName;

        $this->AliasNbPages();
        $this->AddPage();
        $this->SetFont('Arial', '', 9);

        $this->printEntities();

        return $this->Output('I', 'Liste.pdf');
    }

}
