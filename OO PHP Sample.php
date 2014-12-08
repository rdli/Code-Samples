<?php
/**
 * Description:	Object-oriented PHP to implement a database design pattern
 * Author:  Richard Li
 *
 *
 * Table `upldimg` schema, which the code based on
 * 
 * CREATE TABLE IF NOT EXISTS `upldimg` (
 *	  `id` int(8) NOT NULL AUTO_INCREMENT,
 *	  `name` varchar(40) NOT NULL,
 *	  `type` varchar(30) NOT NULL,
 *	  `image` mediumblob NOT NULL,
 *	  `comment` varchar(80) DEFAULT NULL,
 *	  PRIMARY KEY (`id`),
 *	  KEY `name` (`name`)
 *	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * 
 *
*/

// CRUD table interface
interface Dao {
	function insertImg( $name, $type, $fp, $comment );
	function deleteImg( $imgname, $filename );
	function selectImg( );
	function selectName( $filename );
}

// CRUD table implement
class ImgDao implements Dao {
	private $db = null;

	function __construct($dbconnct) {
		$this->db = $dbconnct;
	}
	
	function insertImg($name, $type, $fp, $comment) {
		$db = $this->db;
		try {
			$stmt = $db->prepare ( "insert into upldimg (name, type, image, comment) values (:name, :type, :image, :comment)" );
		
			$stmt->bindParam ( ":name", $name, PDO::PARAM_STR, 40 );
			$stmt->bindParam ( ":type", $type, PDO::PARAM_STR, 30 );
			$stmt->bindParam ( ":image", $fp, PDO::PARAM_LOB );
			$stmt->bindParam ( ":comment", $comment, PDO::PARAM_STR, 80 );
		
			$db->beginTransaction ();
			$execflag = $stmt->execute ();
			$db->commit ();
	
		} catch ( Exception $e ) {
			$stmt->rollBack ();
			echo "Failed to insert image data. " . $e->getMessage ();			
			exit();
		}
	
		if ($execflag) 
			$disptxt =  "Image <b>". $name. "</b> was inserted to database table successfully.";
		else
			$disptxt = "";
	
		$stmt = NULL;
		
		return $disptxt;
		
	}

	function deleteImg($imgname, $filename) {
		$db = $this->db;	
		try {
			$db = $this->db;
			$stmt = $db->prepare ( "delete from upldimg where name = :name" );
		
			$stmt->bindParam ( ":name", $imgname, PDO::PARAM_STR, 40 );
		
			$execflag = $stmt->execute ();
		
		} catch ( Exception $e ) {
			echo "Failed to delte image data, delete it manaually. " . $e->getMessage ();
			exit();
		}
		
		if ($execflag) {
			// Delete thumbnail image here
			echo "Delete image and related thumbnail are successful";
		}
		
		$stmt = NULL;		
	}
	
	function selectImg() {
		$db = $this->db;	
		try {
			$stmt = $db->query ( "select name from upldimg order by id desc" );
		
			$imglists = array ();
			while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
				$imglists [] = $row ["name"];
			}

		} catch ( Exception $e ) {
			echo "Failed to fetch image data. " . $e->getMessage ();
			exit();
		}
	
		reset ( $imglists );
	
		return $imglists;		
	}
	
	function selectName($filename) {
	
		$db = $this->db;
		
		try {
		
			$stmt = $db->prepare ( "select type, image, comment from upldimg where name = :name" );
			
			$stmt->bindParam ( ":name", $filename, PDO::PARAM_STR, 40 );
			$stmt->bindColumn ( 1, $type, PDO::PARAM_STR, 30 );
			$stmt->bindColumn ( 2, $image, PDO::PARAM_LOB );
			$stmt->bindColumn ( 3, $comment, PDO::PARAM_STR, 80 );
			
			$stmt->execute ();
			$stmt->fetch ( PDO::FETCH_BOUND );
		
		} catch ( Exception $e ) {
			die ( "Fail to fetch image data. " . $e->getMessage () );
		}

		$stmt = NULL;
		
		if ($image) {
			$image = base64_encode ( $image );
			$comment = stripslashes ( $comment );
			
			$str = <<<EOD
				<p>&nbsp;</p> <p>&nbsp;</p>
				<div align="center"> 
					<img src="data:$type;base64,$image" alt="$comment" title="image type:$type" />
				</div>
EOD;
			return $str;
		} else {
			
			return "image is not exist";		
		}
	}
}

// Abstract DaoFactory class
abstract class DaoFactory {
	const IMGDAO = 1;
	
	private static $singleimgdao = null;

	static function getDaoFactory($daotype, $dbhost = "localhost", $dbname = "xfiddlec_max", $user= "xfiddlec_user", $password= "public" ) {
		if (($daotype == self::IMGDAO) && (is_null( self::$singleimgdao ) || (! (self::$singleimgdao instanceof ImgDaoFactory))) )
				self::$singleimgdao = new ImgDaoFactory( $dbhost, $dbname, $user, $password );
		
		return self::$singleimgdao;	
	}
	
	abstract function getDao();

}

// Abstract DaoFactory implement
class ImgDaoFactory extends DaoFactory {
	private $dbhost = null;
	private $dbname = null;
	private $user= null;
	private $pword= null;
	
	public $singledb = null;
	public $singledao = null;
	
	function __construct($dbhost, $dbname, $user, $password) {
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->user = $user;
		$this->pword = $password;
	}
	
	function createConection(){
	
		if (is_null( $this->singledb ) || (! ($this->singledb instanceof PDO))) {
			
			try {			
				$dsn = "mysql:host=" . $this->dbhost . ";dbname=" . $this->dbname . ";port=3306";
				$this->singledb = new PDO ( $dsn, $this->user, $this->pword );				
			} catch ( Exception $e ) {
				echo "Unable to connect database. " . $e->getMessage ();
				exit();
			}
		}
	}
	
	function getDao() {
	
		if (is_null( $this->singledao ) || (! ($this->singledao instanceof ImgDao))) {
			if (is_null( $this->singledb )) {
				echo "please call getDao() after createConection()";
				exit();
			} else $this->singledao = new ImgDao($this->singledb);
		}	
		return $this->singledao;
	}
}

// Usage of the code
$imgdaofact = DaoFactory::getDaoFactory(DaoFactory::IMGDAO);
$imgdaofact->createConection();
$imgdao = $imgdaofact->getDao();
//...

?>
