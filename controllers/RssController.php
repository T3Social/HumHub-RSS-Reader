<?php

namespace sij\humhub\modules\rss\controllers;

use Yii;
use yii\web\HttpException;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\content\components\ContentContainerController;
use sij\humhub\modules\rss\models\ConfigureForm;
use sij\humhub\modules\rss\jobs\GetFeedUpdates;

class RssController extends ContentContainerController
{

    /**
     * Configuration Action for Space Admins
     */
    public function actionConfig()
    {
        $container = $this->contentContainer;
        $form = new ConfigureForm();
        $form->url = $container->getSetting('url', 'rss');
        $form->article = $container->getSetting('article', 'rss', 'summary');
        $form->pictures = $container->getSetting('pictures', 'rss', 'yes');
        $form->maxwidth = $container->getSetting('maxwidth', 'rss', '500');
        $form->maxheight = $container->getSetting('maxheight', 'rss', '500');
        $form->interval = $container->getSetting('interval', 'rss', '60');
        if ( $form->load(Yii::$app->request->post()) && $form->validate() ) {
            $container->setSetting('url', $form->url, 'rss');
            $container->setSetting('article', $form->article, 'rss');
            $container->setSetting('pictures', $form->pictures, 'rss');
            $container->setSetting('maxwidth', $form->maxwidth, 'rss');
            $container->setSetting('interval', $form->interval, 'rss');
            Yii::$app->queue->push(new GetFeedUpdates(['space' => $container, 'force' => true]));
            return $this->redirect($container->createUrl('/rss/rss/config'));
        }
        return $this->render('config', array('model' => $form));
    }

}
