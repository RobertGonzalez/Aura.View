<?php
namespace Aura\View;

/**
 * Test class for TwoStep.
 * Generated by PHPUnit on 2011-12-26 at 08:46:27.
 */
class TwoStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwoStep
     */
    protected $twostep;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // remove and create a tmp dir
        $this->tmp = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'tmp';
        if (is_dir($this->tmp)) {
            $this->rmrf($this->tmp);
        }
        mkdir($this->tmp, 0777, true);
        
        // prepare a set of directories for paths
        $dirs = ['foo', 'bar', 'baz'];
        foreach ($dirs as $dir) {
            $this->dirs[$dir] = $this->tmp . DIRECTORY_SEPARATOR . $dir;
            mkdir($this->dirs[$dir], 0777, true);
        }
        
        // put an inner view in 'foo'
        $file = $this->dirs['foo'] . DIRECTORY_SEPARATOR . 'inner_view.php';
        $code = '<?php echo $this->inner_var; ?>';
        file_put_contents($file, $code);
        
        // put an outer view in 'baz'
        $file = $this->dirs['baz'] . DIRECTORY_SEPARATOR . 'outer_view.php';
        $code = '<div><?php echo $this->outer_var . " " . $this->inner_view; ?></div>';
        file_put_contents($file, $code);
        
        // set up the TwoStep view
        $finder = new TemplateFinder();
        
        $helper_locator = new HelperLocator;
        $helper_locator->set('mockHelper', function () {
            return new \Aura\View\Helper\MockHelper;
        });
        
        $template = new Template($finder, $helper_locator);
        
        $format_types = new FormatTypes;
        
        $this->twostep = new TwoStep($template, $format_types);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->rmrf($this->tmp);
    }

    protected function rmrf($dir)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $path) {
            if ($path->isDir()) {
                rmdir($path->__toString());
            } else {
                unlink($path->__toString());
            }
        }
        rmdir($dir);
    }
    
    public function testSetAndGetAccept()
    {
        $expect = [
            'text/html' => '1.0',
            'application/xhtml+xml' => '0.9',
        ];
        
        $this->twostep->setAccept($expect);
        $actual = $this->twostep->getAccept();
        $this->assertSame($expect, $actual);
    }

    public function testSetAndGetFormat()
    {
        $expect = '.html';
        $this->twostep->setFormat($expect);
        $actual = $this->twostep->getFormat();
        $this->assertSame($expect, $actual);
    }

    public function testSetAndGetData()
    {
        $inner_data = ['foo' => 'bar', 'baz' => 'dib'];
        $outer_data = ['zim' => 'gir', 'irk' => 'doom'];
        $this->twostep->setInnerData($inner_data);
        $this->twostep->setOuterData($outer_data);
        $this->assertSame($inner_data, $this->twostep->getInnerData());
        $this->assertSame($outer_data, $this->twostep->getOuterData());
    }

    public function testSetAddAndGetInnerPaths()
    {
        $expect = [$this->dirs['foo'], $this->dirs['bar']];
        $this->twostep->setInnerPaths($expect);
        $actual = $this->twostep->getInnerPaths();
        $this->assertSame($expect, $actual);
        
        $this->twostep->addInnerPath($this->dirs['baz']);
        $expect[] = $this->dirs['baz'];
        $actual = $this->twostep->getInnerPaths();
        $this->assertSame($expect, $actual);
    }

    public function testGetInnerView_none()
    {
        // note that we never set the inner view
        $actual = $this->twostep->getInnerView();
        $this->assertNull($actual);
    }
    
    public function testSetAndGetInnerView_noFormat()
    {
        $expect = 'foo.php';
        $this->twostep->setInnerView($expect);
        $actual = $this->twostep->getInnerView();
        $this->assertSame($expect, $actual);
    }

    public function testSetAndGetInnerView_single()
    {
        $expect = 'foo.php';
        $this->twostep->setInnerView($expect);
        $actual = $this->twostep->getInnerView('.html');
        $this->assertSame($expect, $actual);
    }
    
    public function testSetAndGetInnerView_array()
    {
        $expect = [
            '.html' => 'inner.php',
            '.json' => 'inner.json.php',
        ];
        
        $this->twostep->setInnerView($expect);
        
        // get all formats
        $actual = $this->twostep->getInnerView();
        $this->assertSame($expect, $actual);
        
        // get where format is set
        $expect = 'inner.php';
        $actual = $this->twostep->getInnerView('.html');
        $this->assertSame($expect, $actual);
        
        // get where format is not set
        $this->assertFalse($this->twostep->getInnerView('.xml'));
    }
    
    public function testSetAddAndGetOuterPaths()
    {
        $expect = [$this->dirs['foo'], $this->dirs['bar']];
        $this->twostep->setOuterPaths($expect);
        $actual = $this->twostep->getOuterPaths();
        $this->assertSame($expect, $actual);
        
        $this->twostep->addOuterPath($this->dirs['baz']);
        $expect[] = $this->dirs['baz'];
        $actual = $this->twostep->getOuterPaths();
        $this->assertSame($expect, $actual);
    }
    
    public function testGetOuterView_none()
    {
        // note that we never set the outer view
        $actual = $this->twostep->getOuterView();
        $this->assertNull($actual);
    }
    
    public function testSetAndGetOuterView_noFormat()
    {
        $expect = 'foo.php';
        $this->twostep->setOuterView($expect);
        $actual = $this->twostep->getOuterView();
        $this->assertSame($expect, $actual);
    }

    public function testSetAndGetOuterView_single()
    {
        $expect = 'foo.php';
        $this->twostep->setOuterView($expect);
        $actual = $this->twostep->getOuterView('.html');
        $this->assertSame($expect, $actual);
    }
    
    public function testSetAndGetOuterView_array()
    {
        $expect = [
            '.html' => 'outer.php',
            '.json' => 'outer.json.php',
        ];
        
        $this->twostep->setOuterView($expect);
        
        // get all formats
        $actual = $this->twostep->getOuterView();
        $this->assertSame($expect, $actual);
        
        // get where format is set
        $expect = 'outer.php';
        $actual = $this->twostep->getOuterView('.html');
        $this->assertSame($expect, $actual);
        
        // get where format is not set
        $this->assertFalse($this->twostep->getOuterView('.xml'));
    }

    public function testSetAndGetInnerViewVar()
    {
        $expect = 'some_var_name';
        $this->twostep->setInnerViewVar($expect);
        $actual = $this->twostep->getInnerViewVar();
        $this->assertSame($expect, $actual);
    }
    
    public function testRenderInnerView()
    {
        $this->twostep->setInnerView('inner_view');
        $this->twostep->setInnerData(['inner_var' => 'World!']);
        $this->twostep->setInnerPaths([$this->dirs['bar'], $this->dirs['foo']]);
        $expect = 'World!';
        $actual = $this->twostep->renderInnerView();
        $this->assertSame($expect, $actual);
    }
    
    public function testRenderInnerView_none()
    {
        $actual = $this->twostep->renderInnerView();
        $this->assertNull($actual);
    }

    public function testRenderInnerView_closure()
    {
        $func = function() { return 'World!'; };
        
        $this->twostep->setInnerView($func);
        $expect = 'World!';
        $actual = $this->twostep->renderInnerView();
        $this->assertSame($expect, $actual);
    }

    public function testRenderOuterView()
    {
        $this->twostep->setOuterView('outer_view');
        $this->twostep->setOuterData(['outer_var' => 'Hello']);
        $this->twostep->setOuterPaths([$this->dirs['bar'], $this->dirs['baz']]);
        
        $inner = 'World!';
        $expect = '<div>Hello World!</div>';
        $actual = $this->twostep->renderOuterView($inner);
        $this->assertSame($expect, $actual);
    }
    
    public function testRenderOuterView_none()
    {
        $inner = 'World!';
        $actual = $this->twostep->renderOuterView($inner);
        $this->assertSame($inner, $actual);
    }

    public function testRenderOuterView_closure()
    {
        $func = function($inner) { return "<div>Hello {$inner}</div>"; };
        $this->twostep->setOuterView($func);
        
        $inner = 'World!';
        $expect = '<div>Hello World!</div>';
        $actual = $this->twostep->renderOuterView($inner);
        $this->assertSame($expect, $actual);
    }

    // don't set a format, let it negotiate one from accept headers
    public function testRender()
    {
        $this->twostep->setAccept([
            'text/html' => 1.0,
            'application/json' => 0.9,
        ]);
        
        $view = $this->twostep;
        $this->twostep->setInnerView([
            '.html' => 'inner_view',
            '.json' => function() use ($view) {
                return json_encode($view->getInnerData());
            },
        ]);
        
        $this->twostep->setInnerData(['inner_var' => 'World!']);
        $this->twostep->setInnerPaths([$this->dirs['bar'], $this->dirs['foo']]);
        
        $this->twostep->setOuterView('outer_view');
        $this->twostep->setOuterData(['outer_var' => 'Hello']);
        $this->twostep->setOuterPaths([$this->dirs['bar'], $this->dirs['baz']]);
        
        $expect = '<div>Hello World!</div>';
        $actual = $this->twostep->render();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testRender_noAcceptFormats()
    {
        $this->twostep->setInnerView('inner_view');
        $this->twostep->setInnerData(['inner_var' => 'World!']);
        $this->twostep->setInnerPaths([$this->dirs['bar'], $this->dirs['foo']]);
        $this->twostep->setOuterView('outer_view');
        $this->twostep->setOuterData(['outer_var' => 'Hello']);
        $this->twostep->setOuterPaths([$this->dirs['bar'], $this->dirs['baz']]);
        $expect = '<div>Hello World!</div>';
        $actual = $this->twostep->render();
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @todo Implement testGetContentType().
     */
    public function testGetContentType()
    {
        $this->twostep->setFormat('.html');
        $expect = 'text/html';
        $actual = $this->twostep->getContentType();
        $this->assertSame($expect, $actual);
    }
}
