<?php

namespace App;
use Random\RandomException;
class Template
{
    protected array $pageDbData;
    protected string $pageSlug;
    protected string $pageHtmlContent;
    protected array $pageHtmlZones;

    protected string $defaultCol = '12';
    protected array $allMetaKeys = [];
    protected string $html = '';

    public function __construct(string $pageSlug, mixed $pageDbData, bool $getHtmlContent = false)
    {
        $this->pageSlug = $pageSlug;
        $this->pageDbData = extractFromObjArr($pageDbData, 'metaKey');
        $this->pageHtmlContent = getFileContent($this->pageSlug);
        $this->set($getHtmlContent);
    }

    /**
     * Show content
     */
    public function show(): void
    {
        echo !empty($this->html) ? $this->html : '';
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return !empty($this->html) ? $this->html : $this->pageHtmlContent;
    }

    /**
     * @param bool $getHtmlContent
	 * @throws RandomException
     */
    public function set(bool $getHtmlContent = false): void
    {

        //Check zones types
        if (preg_match_all("/{{(.*?)}}/", $this->pageHtmlContent, $match)) {

            //Check if return admin zone template or content
            if (!$getHtmlContent) {

                //Get zones types
                $this->pageHtmlZones = $this->getZones($match[1]);

                foreach ($this->pageHtmlZones as $adminZone) {

                    $this->html .= $this->buildHtmlAdminZone($adminZone);
                }

            } else {

                //Get zones content
                $this->pageHtmlZones = $match;
                $this->html = $this->buildHtmlFrontZone();

            }
        }
    }

    /**
     * @return string
     */
    public function buildHtmlFrontZone(): string
    {

        foreach ($this->pageHtmlZones[1] as $i => $adminZone) {

            if (strpos($adminZone, '_')) {

                //Get data
                list($metaKey, $formType, $params) = array_pad(explode('_', $adminZone), 3, '');

                $DbZone = $this->pageDbData[$metaKey];
                $value = !empty($DbZone) ? $this->formatText($DbZone->metaValue, $formType) : '';

                //Get input params
                if (!empty($value) && preg_match_all("/\[(.*?)\]/", $params, $match)) {

                    $zoneAddedOptions = $this->getParams($match[1][0]);
                    $value = $this->buildHtmlFrontAdded($formType, $zoneAddedOptions, $value);
                }

                //Set data
                $this->pageHtmlContent = str_replace($this->pageHtmlZones[0][$i], sprintf('%s', $value), $this->pageHtmlContent);

            } else {

                $this->pageHtmlContent = str_replace($this->pageHtmlZones[0][$i], '', $this->pageHtmlContent);
            }
        }

        return $this->pageHtmlContent;
    }

    /**
     * @param string $zone
     * @return string
     */
    public function buildHtmlAdminZone(string $zone): string
    {
        $html = '';
        $col = $this->defaultCol;

        // Vérifie s'il s'agit d'une zone avec un champ de formulaire
        if (str_contains($zone, '_')) {

            // Décompose la zone : metaKey_formType_[params]
            [$metaKey, $formType, $params] = array_pad(explode('_', $zone), 3, '');

            $metaKeyDisplay = ucfirst(str_replace('-', ' ', $metaKey));
            $idCmsContent = $this->pageDbData[$metaKey]->id ?? '';
            $valueCmsContent = $this->pageDbData[$metaKey]->metaValue ?? '';

            // Gestion des colonnes et paramètres
            if (preg_match_all('/\[(.*?)\]/', $params, $match)) {
                $zoneAddedOptions = $this->getParams($match[1][0]);
                $col = $zoneAddedOptions['col'] ?? $col;
            } elseif (!empty($params)) {
                $col = $params;
            }

            $html .= '<div class="col-12 col-lg-' . $col . ' my-2 templateZoneInput">';

            // Si le champ n'a pas encore été traité
            if (!in_array($metaKey, $this->allMetaKeys)) {

                $attributes = 'data-idcmscontent="' . $idCmsContent . '"';

                if (str_contains($formType, ':')) {
                    $options = explode(':', $formType);
                    $formType = array_shift($options);

                    if ($formType === 'select') {
                        $html .= Form::select(
                            $metaKeyDisplay,
                            $metaKey,
                            array_combine($options, $options),
                            $valueCmsContent,
                            false,
                            $attributes
                        );
                    }
                } else {
                    $html .= match ($formType) {
                        'textBig' => Form::textarea($metaKeyDisplay, $metaKey, $valueCmsContent, 8, false, $attributes, ''),
                        'textarea' => Form::textarea($metaKeyDisplay, $metaKey, htmlSpeCharDecode($valueCmsContent), 8, false, $attributes, 'appoeditor'),
                        'urlFile' => Form::text($metaKeyDisplay, $metaKey, 'url', $valueCmsContent, false, 250, $attributes . ' rel="cms-img-popover"', '', 'urlFile'),
                        default => Form::text($metaKeyDisplay, $metaKey, $formType, $valueCmsContent, false, 250, $attributes, '', ''),
                    };
                }

                $this->allMetaKeys[] = $metaKey;
            }

            $html .= '</div>';

        } else {
            $html .= $zone;
        }

        return $html;
    }

    /**
     * @param array $zones
     * @return array
	 * @throws RandomException
     */
    public function getZones(array $zones): array
    {
        //Clean data
        $zones = cleanRequest($zones);

        //Zones types array
        $pageHtmlZonesTypes = [];

        foreach ($zones as $adminZone) {

            //Check for form type
            if (str_contains($adminZone, '_')) {

                //Get data
                list($metaKey, $formType, $col) = array_pad(explode('_', $adminZone), 3, '');

                //Check form type with options
                if (str_contains($formType, ':')) {

                    $options = explode(':', $formType);
                    $formType = array_shift($options);
                }

                //Check form authorised data
                if ($this->isAuthorisedFormType($formType)) {

                    //Filter uniques form zones
                    if (!in_array($adminZone, $pageHtmlZonesTypes)) {
                        $pageHtmlZonesTypes[] = $adminZone;
                    }
                }

            } else {
                if (str_contains($adminZone, '#')) {

                    //Get data
                    list($htmlTag, $text, $zoneName) = array_pad(explode('#', $adminZone), 3, random_int(999, 9999));

                    //Get Container Classes
                    $extract = $this->extractClassFromHtmlTag($htmlTag);
                    $htmlTag = $extract['tag'];
                    $class = $extract['class'];

                    //Check container authorised data
                    if ($this->isAuthorisedHtmlContainer($htmlTag)) {

                        $pageHtmlZonesTypes[] = '<' . $htmlTag . ' class="templateZoneTag templateZoneTitle ' . $class . ' " id="' . $zoneName . '">' . ucfirst($text) . '</' . $htmlTag . '>';
                    }

                } else {

                    //Get closed html tag condition
                    $closeTag = false;
                    if (str_contains($adminZone, '/')) {
                        $closeTag = true;
                        $adminZone = str_replace('/', '', $adminZone);
                    }

                    //Get Container Classes
                    $extract = $this->extractClassFromHtmlTag($adminZone);
                    $htmlTag = $extract['tag'];
                    $class = $extract['class'];

                    //Check authorised html tag
                    if ($this->isAuthorisedHtmlContainer($htmlTag)) {
                        $pageHtmlZonesTypes[] = '<' . ($closeTag ? '/' : '') . $htmlTag . ' class="templateZoneTag ' . $class . ' ">';
                    }

                }
            }
        }

        return $pageHtmlZonesTypes;
    }

    /**
     * @param string $formType
     * @param array $options
     * @param string $value
     * @return string
     */
    public function buildHtmlFrontAdded(string $formType, array $options, string $value): string
    {

        if ($formType === 'urlFile') {

            $imgDefaultOptions = array(
                'webp' => false
            );

            $options = array_merge($imgDefaultOptions, $options);

            if (array_key_exists('size', $options) && strpos($value, '://')) {
                $value = str_replace('://', '', strstr($value, '://'));
                if (strpos($value, '/')) {
                    $url = explode('/', $value);
                    return getThumb(array_pop($url), $options['size'], $options['webp']);
                }
            }
        }

        return $value;
    }


    public function getParams(string $match): array
    {

        $options = [];
        $params = array_filter(explode(';', $match));
        foreach ($params as $param) {
            list($key, $val) = explode('=', $param);
            $options[$key] = $val;
        }
        return $options;
    }

    public function formatText(?string $text, string $type): string
    {
        $text = htmlSpeCharDecode($text ?? '');

        return $type === 'textBig' ? nl2br($text) : $text;
    }

    /**
     * @param mixed $formType
     * @return bool
     */
    public function isAuthorisedFormType(mixed $formType): bool
    {

        //Authorised form manage data
        $acceptedFormType = array('text', 'textarea', 'textBig', 'email', 'tel', 'url', 'color', 'number', 'date', 'select', 'radio', 'checkbox', 'urlFile');

        return in_array($formType, $acceptedFormType);
    }

    /**
     * @param mixed $formType
     * @return bool
     */
    public function isAuthorisedHtmlContainer(mixed $formType): bool
    {

        //Authorised HTML Container
        $acceptedHtmlContainer = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'strong', 'em', 'div', 'hr', 'br');

        return in_array($formType, $acceptedHtmlContainer);
    }

    /**
     * @param string $htmlTag
     * @return array
     */
    public function extractClassFromHtmlTag(string $htmlTag = ''): array
    {
        $class = '';
        if (strpos($htmlTag, '.')) {
            list($htmlTag, $class) = explode('.', $htmlTag, 2);

            if (strpos($class, '.')) {
                $class = str_replace('.', ' ', $class);
            }
        }
        return array('tag' => $htmlTag, 'class' => $class);
    }

    /**
     * @return string
     */
    public function showErrorPage(): string
    {
        return '<div class="container"><h4>' . trans('Cette page n\'existe pas') . '</h4></div>';
    }
}