<?php
declare(strict_types=1);

namespace HauerHeinrich\HhWidgetHhnews\Widgets;

use \Psr\Http\Message\ServerRequestInterface;
use \TYPO3\CMS\Backend\View\BackendViewFactory;
use \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as Cache;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;

class HhnewsWidget
    implements
        \TYPO3\CMS\Dashboard\Widgets\WidgetInterface,
        \TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface,
        \TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface,
        \TYPO3\CMS\Dashboard\Widgets\AdditionalJavaScriptInterface {

    private WidgetConfigurationInterface $configuration;
    private BackendViewFactory $backendViewFactory;
    private Cache $cache;
    private array $button;
    private array $options;
    private ServerRequestInterface $request;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        BackendViewFactory $backendViewFactory,
        Cache $cache,
        array $button = [],
        array $options = []
    ) {
        $this->configuration = $configuration;
        $this->backendViewFactory = $backendViewFactory;
        $this->cache = $cache;
        $this->button = $button;
        $this->options = array_merge(
            [
                'limit' => 5,
                'lifeTime' => 43200,
            ],
            $options
        );
    }

    public function setRequest(ServerRequestInterface $request): void {
        $this->request = $request;
    }

    /**
     * This method returns the content of a widget. The returned markup will be delivered by an AJAX call and will not be escaped.
     *
     * @return string
     */
    public function renderWidgetContent(): string {
        $view = $this->backendViewFactory->create($this->request, ['typo3/cms-dashboard', 'hauerheinrich/hh-widget-hhnews']);
        $view->setTemplate('HhnewsWidget');
        $view->assignMultiple([
            'items' => $this->getRssItems(),
            'button' => $this->button,
            'options' => $this->options,
            'configuration' => $this->configuration,
        ]);

        return $view->render();
    }

    /**
     * This method returns the options of the widget as set in the registration.
     *
     * @return array
     */
    public function getOptions(): array {
        return [];
    }

    /**
     * Get RSS feed from URL
     *
     * @return array
     */
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

    /**
     * Add plain css file(s) to the dashboard widget
     *
     * @return array
     */
    public function getCssFiles(): array {
        return [
            'EXT:hh_widget_hhnews/Resources/Public/Css/widgets.css'
        ];
    }

    /**
     * Add plain javascript file(s) to the dashboard widget
     *
     * @return array
     */
    public function getJsFiles(): array {
        return [
            // 'EXT:my_extension/Resources/Public/JavaScript/file.js',
        ];
    }
}
