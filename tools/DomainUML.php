<?php
/**
 * Examples of Graphviz UML class diagrams
 *
 * PHP version 7
 *
 * @category PHP
 * @package  UmlWriter
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause  The 3-Clause BSD License
 */

require_once __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/ShortGraphVizGenerator.php';

use Bartlett\UmlWriter\Service\ClassDiagramRenderer;
use Symfony\Component\Finder\Finder;
use Graphp\GraphViz\GraphViz;

$options = [
    'show_properties' => true,
    'show_private' => true,
    'show_protected' => true,
    'add_parents' => false,
    'label_format' => 'html',
    'graph.rankdir' => 'LR',
    'graph.bgcolor' => 'white',
    'node.fillcolor' => 'lightgrey',
    'node.style' => 'solid',
    // @link https://plantuml.com/en/color
    'cluster.Psr\\Container.graph.bgcolor' => 'LightSkyBlue',
    'cluster.Symfony\\Component\\Console.graph.bgcolor' => 'LightSkyBlue',
    'cluster.Symfony\\Component\\Console\\Command.graph.bgcolor' => 'LightSkyBlue',
    'cluster.Symfony\\Component\\Config\\Loader.graph.bgcolor' => 'LightSkyBlue',
    'cluster.Symfony\\Contracts\\Service.graph.bgcolor' => 'LightSkyBlue',
    'cluster.Bartlett\\UmlWriter\\Service.graph.bgcolor' => 'BurlyWood',
    'cluster.Bartlett\\UmlWriter\\Console.graph.bgcolor' => 'BurlyWood',
    'cluster.Bartlett\\UmlWriter\\Console\\Command.graph.bgcolor' => 'BurlyWood',
    'cluster.Bartlett\\UmlWriter\\Config\\Loader.graph.bgcolor' => 'BurlyWood',
    'cluster.Bartlett\\UmlWriter\\Generator.graph.bgcolor' => 'BurlyWood',
];

/** @var GraphVizGenerator $generator */
$generator = new ShortGraphVizGenerator(new GraphViz());
$renderer = new ClassDiagramRenderer();

$dstFolder = "tools/DomainUML";
if (!file_exists($dstFolder)) {
    mkdir($dstFolder);
}

$dataSource = __DIR__ . '/../src/App/Domain';
$finder = new Finder();
foreach ($finder->in($dataSource)->depth('== 0')->directories() as $path) {
    $aggregateFinder = new Finder();
    $aggregateFinder->in($path)->name("*.php");

    $script = $renderer($aggregateFinder, $generator, $options);

    $graph = $renderer->getGraph();
    $filename = basename($path);
    $target = $generator->createImageFile($graph, "%E -T%F %t -o {$dstFolder}/{$filename}.png");
    echo(empty($target) ? 'no' : $target) . ' file generated' . PHP_EOL;
}
