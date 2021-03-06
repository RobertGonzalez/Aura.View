<?php
namespace Aura\View;

/**
 * Test class for Template.
 * Generated by PHPUnit on 2011-03-27 at 14:44:16.
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
    }
    
    protected function newTemplate(array $paths = [])
    {
        $finder = new TemplateFinder();
        
        $helper_locator = new HelperLocator;
        $helper_locator->set('mockHelper', function () {
            return new \Aura\View\Helper\MockHelper;
        });
        
        $template = new Template($finder, $helper_locator);
        $template->setPaths($paths);
        
        return $template;
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @todo Implement test__get().
     */
    public function test__setGetIssetUnset()
    {
        $template = $this->newTemplate();
        $this->assertFalse(isset($template->foo));
        $template->foo = 'bar';
        $this->assertTrue(isset($template->foo));
        $this->assertSame('bar', $template->foo);
        unset($template->foo);
        $this->assertFalse(isset($template->foo));
    }
    
    public function test__call()
    {
        $template = $this->newTemplate();
        $actual = $template->mockHelper();
        $this->assertSame('Hello Helper', $actual);
    }
    
    /**
     * @todo Implement testSetData().
     */
    public function testAddSetAndGetData()
    {
        $template = $this->newTemplate();
        $expect = [];
        $actual = $template->getData();
        $this->assertSame($expect, $actual);
        
        // add data
        $template->foo = 'bar';
        $template->addData(['baz' => 'dib']);
        $expect = [
            'foo' => 'bar',
            'baz' => 'dib',
        ];
        $actual = $template->getData();
        $this->assertSame($expect, $actual);
        
        // set data
        $data = [
            'foo' => 'bar'
        ];
        $template->setData($data);
        $this->assertSame('bar', $template->foo);
        
        $actual = $template->getData();
        $this->assertSame($data, $actual);
    }
    
    /**
     * @todo Implement testFind().
     */
    public function testFind()
    {
        // prepare a set of directories and files
        $base = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'tmp';
        $list = ['foo', 'bar', 'baz'];
        $dirs = [];
        foreach ($list as $dir) {
            // make dir for the finder
            $dirs[$dir] = $base . DIRECTORY_SEPARATOR . $dir;
            mkdir($dirs[$dir], 0777, true);
            
            // place the same file in each dir
            $file = $dirs[$dir] . DIRECTORY_SEPARATOR . 'zim.php';
            file_put_contents($file, 'empty');
        }
        
        // now find it; should be the same as the one at the beginning
        // of the paths
        $template = $this->newTemplate($dirs);
        $actual = $template->find('zim');
        $expect = $dirs['foo'] . DIRECTORY_SEPARATOR . 'zim.php';
        $this->assertSame($expect, $actual);
        
        // remove the directories and files
        foreach ($dirs as $dir) {
            unlink($dir . DIRECTORY_SEPARATOR . 'zim.php');
            rmdir($dir);
        }
    }
    
    public function testFetch()
    {
        // the template file
        $file = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
              . 'tmp' . DIRECTORY_SEPARATOR
              . 'fetch' . DIRECTORY_SEPARATOR
              . 'zim.php';
        
        // the template code
        $code = '<?php echo $this->foo; ?>';
        
        // put it in place
        $dir = dirname($file);
        mkdir($dir, 0777, true);
        file_put_contents($file, $code);
        
        // get a template object
        $template = $this->newTemplate([$dir]);
        $template->foo = 'bar';
        $actual = $template->fetch('zim');
        $expect = 'bar';
        $this->assertSame($expect, $actual);
        
        // remove the file and dir
        unlink($file);
        rmdir($dir);
    }
    
    
    public function testFetchDirect()
    {
        // the template file
        $file = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
              . 'tmp' . DIRECTORY_SEPARATOR
              . 'fetch' . DIRECTORY_SEPARATOR
              . 'zim.php';
        
        // the template code
        $code = '<?php echo $this->foo; ?>';
        
        // put it in place
        $dir = dirname($file);
        mkdir($dir, 0777, true);
        file_put_contents($file, $code);
        
        // get a template object
        $template = $this->newTemplate();
        $template->foo = 'bar';
        
        // fetch it
        $actual = $template->fetch($file);
        $expect = 'bar';
        $this->assertSame($expect, $actual);
        
        // fetch it again for coverage
        $actual = $template->fetch($file);
        $expect = 'bar';
        $this->assertSame($expect, $actual);
        
        // remove the file and dir
        unlink($file);
        rmdir($dir);
    }
    
    public function testFetchExtract()
    {
        // the template file
        $file = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
              . 'tmp' . DIRECTORY_SEPARATOR
              . 'fetch' . DIRECTORY_SEPARATOR
              . 'foo.php';
        
        // the template code
        $code = '<?php echo $foo; ?>';
        
        // put it in place
        $dir = dirname($file);
        mkdir($dir, 0777, true);
        file_put_contents($file, $code);
        
        // get a template object
        $template = $this->newTemplate([$dir]);
        $actual = $template->fetch('foo', ['foo' => 'dib']);
        $expect = 'dib';
        $this->assertSame($expect, $actual);
        
        // remove the file and dir
        unlink($file);
        rmdir($dir);
    }
    
    /**
     * @expectedException Aura\View\Exception\TemplateNotFound
     */
    public function testFindTemplateNotFound()
    {
        $template = $this->newTemplate();
        $template->find('no_such_template');
    }
    
    public function testGetHelper()
    {
        $template = $this->newTemplate();
        $actual = $template->getHelper('mockHelper');
        $this->assertInstanceOf('Aura\View\Helper\MockHelper', $actual);
        $again = $template->getHelper('mockHelper');
        $this->assertSame($actual, $again);
    }
    
    /**
     * @expectedException Aura\View\Exception\HelperNotMapped
     */
    public function testGetHelperNotMapped()
    {
        $template = $this->newTemplate();
        $actual = $template->getHelper('noSuchHelper');
    }
    
    public function testGetHelperLocator()
    {
        $template = $this->newTemplate();
        $actual = $template->getHelperLocator();
        $this->assertInstanceOf('Aura\View\HelperLocator', $actual);
    }
    
    public function testGetTemplateFinder()
    {
        $template = $this->newTemplate();
        $actual = $template->getTemplateFinder();
        $this->assertInstanceOf('Aura\View\TemplateFinder', $actual);
    }
}
