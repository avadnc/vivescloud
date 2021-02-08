<?php

class CfdiAddendaImporter
{
    private const CFDI_NS = 'http://www.sat.gob.mx/cfd/3';

    /** @var DOMDocument */
    private $cfdi;

    public function __construct(DOMDocument $cfdi)
    {
        $this->cfdi = $cfdi;
    }

    public static function newFromString(string $xmlContent): self
    {
        $document = new DOMDocument();
        $document->loadXml($xmlContent);
        return new self($document);
    }

    public function getComprobanteDocument(): DOMDocument
    {
        return $this->cfdi;
    }

    public function getComprobanteElement(): DOMElement
    {
        return $this->cfdi->documentElement;
    }

    public function asXml(): string
    {
        return $this->cfdi->saveXml();
    }

    public function importAddendaXmlContent(string $addendaContent): void
    {
        $addendaElement = $this->findOrCreateFirstAddendaElement();

        $addendaDocument = new DOMDocument();
        if(!$addendaDocument->loadXml($addendaContent))
        {
          throw new Exception($addendaContent);  
        }
        $this->importAddendaXmlElement($addendaDocument->documentElement);
    }

    public function importAddendaXmlElement(DOMElement $addendaContentElement): void
    {
        $addendaElement = $this->findOrCreateFirstAddendaElement();
        $importedElement = $this->cfdi->importNode($addendaContentElement, true);
        $addendaElement->appendChild($importedElement);
    }

    public function findOrCreateFirstAddendaElement(): DOMElement
    {
        $addendaElement = $this->findFirstElement('//cfdi:Comprobante/cfdi:Addenda');
        if (null === $addendaElement) {
            $addendaElement = $this->cfdi->createElementNS(self::CFDI_NS, 'cfdi:Addenda');
            $this->getComprobanteElement()->appendChild($addendaElement);
        }
        return $addendaElement;
    }

    public function findFirstElement(string $query): ?DOMElement
    {
        $list = $this->findElements($query);
        if ($list->length === 0) {
            return null;
        }
        return $list->item(0);
    }

    public function findElements(string $query): DOMNodeList
    {
        $xpath = new DOMXPath($this->cfdi);
        $list = $xpath->query($query);
        if ($list === false) {
            $list = new DOMNodeList();
        }
        return $list;
    }
}
