<?php
declare(strict_types=1);

namespace HauerHeinrich\HhWidgetHhnews\Widgets;

use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as Cache;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;

class HhnewsWidget implements \TYPO3\CMS\Dashboard\Widgets\WidgetInterface, \TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface {

    /**
     * @var WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var StandaloneView
     */
    private $view;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $button;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        Cache $cache,
        array $button = [],
        array $options = []
    ) {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->cache = $cache;
        $this->button = $button;
        $this->options = array_merge(
            [
                'limit' => 5,
                'lifeTime' => 43200
            ],
            $options
        );
    }

    public function renderWidgetContent(): string {
        $this->view->setTemplate('HhnewsWidget');
        $this->view->assignMultiple([
            'items' => $this->getRssItems(),
            'button' => $this->button,
            'options' => $this->options,
            'configuration' => $this->configuration,
        ]);

        return $this->view->render();
    }

    protected function getRssItems(): array {
        $cacheHash = md5($this->options['feedUrl']);
        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $rssContent = GeneralUtility::getUrl($this->options['feedUrl']);
        if ($rssContent === false) {
            throw new \RuntimeException('RSS URL could not be fetched', 1573385431);
        }

        $rssFeed = simplexml_load_string($rssContent);
        $items = [];
        foreach ($rssFeed->channel->item as $item) {
            $items[] = [
                'title' => trim((string)$item->title),
                'link' => trim((string)$item->link),
                'pubDate' => trim((string)$item->pubDate),
                'description' => trim((string)$item->description),
                'enclosure' => [
                    'url' => trim((string)$item->enclosure->attributes()->url),
                    'type' => trim((string)$item->enclosure->attributes()->type)
                ]

            ];
        }

        usort($items, function ($item1, $item2) {
            return new \DateTime($item2['pubDate']) <=> new \DateTime($item1['pubDate']);
        });
        $items = array_slice($items, 0, $this->options['limit']);

        $this->cache->set($cacheHash, $items, ['dashboard_rss'], $this->options['lifeTime']);

        return $items;
    }

    public function getCssFiles(): array {
        return [
            'EXT:hh_widget_hhnews/Resources/Public/Css/widgets.css'
        ];
    }
}
