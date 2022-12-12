{!! $header !!}
<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02 pain.008.001.02.xsd">
    <CstmrDrctDbtInitn>
        <GrpHdr>
            <MsgId>{{ $msgId }}</MsgId>
            <CreDtTm>{{ $creDtTm }}</CreDtTm>
            <NbOfTxs>{{ $nbOfTxs }}</NbOfTxs>
            <CtrlSum>{{ $ctrlSum }}</CtrlSum>
            <InitgPty>
                <Nm>{!! $nm !!}</Nm>
            </InitgPty>
        </GrpHdr>
        <PmtInf>
            <PmtInfId>{{ $pmtInfId }}</PmtInfId>
            <PmtMtd>DD</PmtMtd>
            <BtchBookg>true</BtchBookg>
            <NbOfTxs>{{ $nbOfTxs }}</NbOfTxs>
            <CtrlSum>{{ $ctrlSum }}</CtrlSum>
            <PmtTpInf>
                <SvcLvl>
                    <Cd>SEPA</Cd>
                </SvcLvl>
                <LclInstrm>
                    <Cd>CORE</Cd>
                </LclInstrm>
                <SeqTp>RCUR</SeqTp>
            </PmtTpInf>
            <ReqdColltnDt>{{ $reqdColltnDt }}</ReqdColltnDt>
            <Cdtr>
                <Nm>{!! $nm !!}</Nm>
            </Cdtr>
            <CdtrAcct>
                <Id>
                    <IBAN>{{ $iban }}</IBAN>
                </Id>
            </CdtrAcct>
            <CdtrAgt>
                <FinInstnId>
                    <BIC>{{ $bic }}</BIC>
                </FinInstnId>
            </CdtrAgt>
            <ChrgBr>SLEV</ChrgBr>
            <CdtrSchmeId>
                <Id>
                    <PrvtId>
                        <Othr>
                            <Id>{{ $sepaId }}</Id>
                            <SchmeNm>
                                <Prtry>SEPA</Prtry>
                            </SchmeNm>
                        </Othr>
                    </PrvtId>
                </Id>
            </CdtrSchmeId>
            @foreach ($payments as $payment)
                <DrctDbtTxInf>
                    <PmtId>
                        <EndToEndId>NOTPROVIDED</EndToEndId>
                    </PmtId>
                    <InstdAmt Ccy="EUR">{{ $payment['instdAmt'] }}</InstdAmt>
                    <DrctDbtTx>
                        <MndtRltdInf>
                            <MndtId>{{ $payment['mndtId'] }}</MndtId>
                            <DtOfSgntr>{{ $payment['dtOfSgntr'] }}</DtOfSgntr>
                            <AmdmntInd>false</AmdmntInd>
                        </MndtRltdInf>
                    </DrctDbtTx>
                    <DbtrAgt>
                        <FinInstnId>
                            <BIC>{{ $payment['bic'] }}</BIC>
                        </FinInstnId>
                    </DbtrAgt>
                    <Dbtr>
                        <Nm>{!! $payment['nm'] !!}</Nm>
                    </Dbtr>
                    <DbtrAcct>
                        <Id>
                            <IBAN>{{ $payment['iban'] }}</IBAN>
                        </Id>
                    </DbtrAcct>
                    <RmtInf>
                        <Ustrd>{!! $payment['ustrd'] !!}</Ustrd>
                    </RmtInf>
                </DrctDbtTxInf>
            @endforeach
        </PmtInf>
    </CstmrDrctDbtInitn>
</Document>
