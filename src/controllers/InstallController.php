<?php

namespace App\Controllers;

use App\Response;
use App\Controller;

use App\models\Question;
use App\models\Setting;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class InstallController extends Controller
{
    const DEF_MIN = '30';
    const DEF_MAX = '70';

	public function index()
	{
		$errors = null;
        $em = $this->getEntityManager();
        if ($em)
            $this->redirect('/');
        if ($this->request->isPost()) {
            $username = $this->request->getPost('username');
			$password = $this->request->getPost('password');
			$dbname = $this->request->getPost('dbname');
			$address = $this->request->getPost('address');
            
            if (!$username)
                $errors[] = 'Необходимо ввести имя пользователя.';
            if (!$password)
                $errors[] = 'Необходимо ввести пароль.';
			if (!$dbname)
                $errors[] = 'Необходимо ввести имя БД.';
			if (!$address)
                $errors[] = 'Необходимо ввести адресс БД.';

            if (!$errors) {
				$config = $this->getConfig($username, $password, $dbname, $address);
				$f = fopen(PROJECT_DIR.'/config/config.php', 'w');
				if (!$f) {
					$errors[] = 'Не удается сохранить файл конфигурации.';
				} else {
					fwrite($f, $config);
					fclose($f);
					try {
						$this->dbInit($username, $password, $dbname, $address);
						$this->setDefaultValues();
                        $this->redirect('/');
					} catch (\Exception $e){
						$errors[] = 'Ошибка базы данных.';
					}
				}
            }
        }
		return new Response('install/index', ['errors' => $errors]);
	}
	
	protected function dbInit($username, $password, $dbname, $address)
	{
		$dbParams = [
			'driver'   => 'pdo_mysql',
			'user'     => $username,
			'password' => $password,
			'dbname'   => $dbname,
			'host'	   => $address,
		];
		$paths = array("/path/to/entity-files");
		$isDevMode = false;
		$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
		$em = EntityManager::create($dbParams, $config);
		\App\Core::getInstance()->setEntityManager($em);
		$em = $this->getEntityManager();
		$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
		$classes = array(
			$em->getClassMetadata('\App\Models\Question'),
			$em->getClassMetadata('\App\Models\Setting'),
			$em->getClassMetadata('\App\Models\Result')
		);
		$tool->dropSchema($classes);
		$tool->createSchema($classes);	
	}
	
	protected function setDefaultValues()
	{
		try {
			$min = self::DEF_MIN;
			$max = self::DEF_MAX;
			$em = $this->getEntityManager();
			$em->getConnection()->beginTransaction();
			$setmin = new Setting ();
			$setmin->setTitle('min')->setValue($min);
			$setmax = new Setting ();
			$setmax->setTitle('max')->setValue($max);
			$em->persist($setmin);
			$em->persist($setmax);
			$em->flush();
			for ($i = 0; $i < 100; $i++) {
				$q = new Question();
                $q->setComplexity(mt_rand($min,$max));
				$q->setNum(0);
				$em->persist($q);
                $em->flush();
			}
			$em->getConnection()->commit();
		} catch (\Exception $e){
			$em->getConnection()->rollback();
            throw new \Exception ('Ошибка базы данных.');
		}
	}
	
	protected function getConfig($username, $password, $dbname, $address)
	{
		$config = <<<END
<?php

dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => '<username>',
    'password' => '<password>',
    'dbname'   => '<dbname>',
	'host'	   => '<address>',
);

END;
		$config = str_replace('<username>', $username, $config);
		$config = str_replace('<password>', $password, $config);
		$config = str_replace('<dbname>', $dbname, $config);
		$config = str_replace('<address>', $address, $config);
		$config = str_replace('dbParams', '$dbParams', $config);
		return $config;
	}

}
