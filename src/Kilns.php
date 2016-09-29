<?php

namespace ABM\Kilns;

// 目录入口
define('KILNS_ROOT_PATH', dirname(__FILE__));
/**
 * Kilns MS Project Oxford SDK in Guzzle
 * SDK入口文件.
 */
class Kilns
{
    /**
     * Academic.
     */
    const ACADEMIC = 'Academic';

    /**
     * Face
     * 人脸识别.
     */
    const FACE = 'Face';

    /**
     * Linguistic.
     */
    const LINGUIST = 'Linguistic';

    /**
     * News searching.
     */
    const NEWS = 'NewsSearch';

    /**
     * Emotion
     * 情绪识别.
     */
    const EMOTION = 'Emotion';

    /**
     * News searching.
     */
    const SEARCH = 'Search';

    /**
     * Spell Check.
     */
    const SPELLING = 'SpellCheck';

    /**
     * Text searching.
     */
    const TEXT = 'TextAnalytics';

    /**
     * Video
     * 视频检测.
     */
    const VIDEO = 'Video';
    /**
     * Vision
     * 计算机视觉.
     */
    const VISION = 'Vision';

    /**
     * WebLM.
     */
    const WEBLM = 'WebLM';

    /**
     * load
     * 加载模块文件.
     *
     * @param string $moduleName   模块名称
     * @param array  $moduleConfig 模块配置
     *
     * @return
     */
    public static function load($moduleName, $moduleConfig = [])
    {
        $moduleName = ucfirst($moduleName);
        $moduleClassFile = KILNS_ROOT_PATH.'/module/'.$moduleName.'.php';
        if (!file_exists($moduleClassFile)) {
            return false;
        }
        require_once $moduleClassFile;
        $moduleClassName = $moduleName;
        $moduleInstance = new $moduleClassName();
        if (!empty($moduleConfig)) {
            $moduleInstance->setConfig($moduleConfig);
        }

        return $moduleInstance;
    }
}
