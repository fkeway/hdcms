<?php
/** .-------------------------------------------------------------------
 * |  Software: [hdcms framework]
 * |      Site: www.hdcms.com
 * |-------------------------------------------------------------------
 * |    Author: 向军大叔 <www.aoxiangjun.com>
 * | Copyright (c) 2012-2019, www.houdunren.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/

namespace App\Servers\Model;

/**
 * 模型创建
 * Class Model
 * @package App\Servers\Model
 */
trait Model
{
    protected function createModel()
    {
        $file = \Storage::drive('module')
            ->path("{$this->module['name']}/Entities/{$this->config['model']}.php");
        if (!is_file($file)) {
            file_put_contents($file, $this->replaceVars(__DIR__ . '/app/model.tpl'));
        }
    }
}