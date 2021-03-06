<?php

namespace Frozennode\Administrator\Fields;

use zgldh\QiniuStorage\QiniuStorage;
use Frozennode\Administrator\Includes\Multup;

class Image extends File
{
    /**
     * The specific defaults for the image class.
     *
     * @var array
     */
    protected $imageDefaults = array(
        'sizes' => array(),
    );

    /**
     * The specific rules for the image class.
     *
     * @var array
     */
    protected $imageRules = array(
        'sizes' => 'array',
    );

    /**
     * This static function is used to perform the actual upload and resizing using the Multup class.
     *
     * @return array
     */
    public function doUpload()
    {
        // CJ: Create a folder if it doesn't already exist
        if (!file_exists($this->getOption('location'))) {
            mkdir($this->getOption('location'), 0777, true);
        }

        //use the multup library to perform the upload
        $result = Multup::open('file', 'image|max:'.$this->getOption('size_limit') * 1000, $this->getOption('location'),
                                    $this->getOption('naming') === 'random')
            ->sizes($this->getOption('sizes'))
            ->set_length($this->getOption('length'))
            ->upload();

        // 增加七牛上传
        $disk = QiniuStorage::disk('qiniu');
        //var_dump($result);
        $content = file_get_contents($result[0]['path']);
        $disk->put('uploads/'. $model . '/' . $result[0]['filename'], $content);

        return $result[0];
    }

    /**
     * Gets all rules.
     *
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();

        return array_merge($rules, $this->imageRules);
    }

    /**
     * Gets all default values.
     *
     * @return array
     */
    public function getDefaults()
    {
        $defaults = parent::getDefaults();

        return array_merge($defaults, $this->imageDefaults);
    }
}
