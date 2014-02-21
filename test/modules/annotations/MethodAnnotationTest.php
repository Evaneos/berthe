<?php
class MethodAnnotationTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->configPath = dirname(dirname(__DIR__)) . '/config/';

        // Autoloader for fixtures
        spl_autoload_register(function($class) {
            $file = str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php";
            $filepath = dirname(dirname(__DIR__)) . '/fixtures/' . $file;
            if (file_exists($filepath)) {
                require $filepath;
            }
            return false;
        });

        Doctrine\Common\Annotations\AnnotationRegistry::registerFile(ROOT_DIR . '/src/Berthe/Annotation/ACL.php');
    }

    public function testDummyMethodAccessibleWithRoleAdmin() {
        $_SESSION['role'] = Role::ADMIN;
        $service = $this->getServiceTest();
        $service->dummyMethod(1, 2);
    }

    /**
     * @expectedException LogicException
     */
    public function testDummyMethodNotAccessibleWithRoleGuest() {
        $_SESSION['role'] = Role::GUEST;
        $service = $this->getServiceTest();
        $service->dummyMethod(1, 2);
    }

    /**
     * @return ServiceTest
     */
    private function getServiceTest() {
        $exceptionInterceptor = new Berthe_Interceptor_Exception(new ServiceTest());
        $aclInterceptor = new Berthe_Interceptor_ACLChecker($exceptionInterceptor);

        return $aclInterceptor;
    }
}