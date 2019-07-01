<?php

namespace Common\DataCollector;

use Frontend\Core\Engine\Block\ExtraInterface;
use Frontend\Core\Engine\Block\ModuleExtraInterface;
use Frontend\Core\Engine\Block\Widget;
use Frontend\Core\Engine\Page;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageContextDataCollector extends DataCollector
{
    /** @var Page|null */
    private $page;

    /** @var string */
    private $theme;

    /** @var string */
    private $sitePath;

    public function __construct(ContainerInterface $container)
    {
        if (!$container->has('page')) {
            return;
        }

        $this->page = $container->get('page');
        $this->theme = $container->get('fork.settings')->get('Core', 'theme', 'Fork');
        $this->sitePath = $container->getParameter('site.path_www');
    }

    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        if ($this->page === null) {
            return;
        }

        $pageRecord = $this->page->getRecord();

        $this->data = [
            'page' => [
                'id' => $this->page->getId(),
                'title' => $pageRecord['title'],
                'template' => $this->getFullTemplatePath($pageRecord['template_path']),
            ],
            'widgets' => $this->getWidgets($this->page->getExtras()),
            'block' => $this->getBlock($this->page->getExtras()),
            'theme' => $this->theme,
        ];
    }

    private function getWidgets(array $pageExtras): ?array
    {
        $pageContextDataCollector = $this;
        $widgets = array_map(
            function (Widget $widget) use ($pageContextDataCollector) {
                return [
                    'action' => $widget->getAction(),
                    'module' => $widget->getModule(),
                    'template' => $pageContextDataCollector->getFullTemplatePath($widget->getTemplatePath()),
                ];
            },
            $this->getClassInstances(Widget::class, $pageExtras)
        );

        return empty($widgets) ? null : $widgets;
    }

    private function getBlock(array $pageExtras): ?array
    {
        /** @var ExtraInterface $action */
        $action = $this->getClassInstances(ExtraInterface::class, $pageExtras)[0] ?? null;

        if ($action === null) {
            return null;
        }

        return [
            'action' => $action->getAction(),
            'module' => $action->getModule(),
            'template' => $this->getFullTemplatePath($action->getTemplatePath()),
        ];
    }

    private function getClassInstances(string $className, array $classes): array
    {
        return array_filter(
            $classes,
            function (ModuleExtraInterface $class) use ($className) {
                return get_class($class) === $className;
            }
        );
    }

    public function getName(): string
    {
        return 'page_context';
    }

    public function reset(): void
    {
        $this->data = [];
    }

    private function getFullTemplatePath(string $templatePath): string
    {
        $themePath = FRONTEND_PATH . '/Themes/' . $this->theme;

        if (file_exists($themePath . '/Modules/' . $templatePath)) {
            return str_replace($this->sitePath, '', $themePath . '/Modules/' . $templatePath);
        }

        return str_replace($this->sitePath, '', FRONTEND_MODULES_PATH . '/' . $templatePath);
    }
}
