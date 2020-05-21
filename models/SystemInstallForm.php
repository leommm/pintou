<?php
/**
 * Created by Adon.
 * User: Adon
 * Date: 2017/10/30
 * Time: 16:51
 */

namespace app\models;

use yii\db\Connection;
use yii\db\Exception;
use yii\helpers\VarDumper;

/**
 * @property Connection $db
 */
class SystemInstallForm extends Model
{
    public $host;
    public $port;
    public $dbname;
    public $tablePrefix;
    public $username;
    public $password;
    public $admin_username;
    public $admin_password;

    public $db;

    public function rules()
    {
        return [
            [['host', 'port', 'dbname', 'tablePrefix', 'username', 'password', 'admin_username', 'admin_password',], 'trim',],
            [['host', 'port', 'dbname', 'tablePrefix', 'username', 'password', 'admin_username', 'admin_password',], 'required',],
        ];
    }

    public function attributeLabels()
    {
        return [
            'host' => '数据库IP',
            'port' => '数据库端口',
            'dbname' => '数据库名',
            'tablePrefix' => '数据表前缀',
            'username' => '数据库用户名',
            'password' => '数据库密码',
            'admin_username' => '管理员用户名',
            'admin_password' => '管理员密码',
        ];
    }

    public function install()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }

        $this->db = new Connection([
            'dsn' => "mysql:host={$this->host};port={$this->port};dbname={$this->dbname}",
            'username' => $this->username,
            'password' => $this->password,
            'charset' => 'utf8',
        ]);
        try {
            $res = $this->db->createCommand("SHOW TABLES LIKE :keyword")->bindValue(':keyword', $this->tablePrefix . '%')->queryAll();
            if ($res) {
                return [
                    'code' => 1,
                    'msg' => "已存在表前缀为{$this->tablePrefix}的数据表，请使用其它表前缀或更换其它数据库",
                ];
            }
        } catch (Exception $exception) {
            return [
                'code' => 1,
                'msg' => "数据库连接失败，请检查数据库信息是否正确，<br>错误信息：{$exception->getCode()}，{$exception->getMessage()}<a target='_blank' href='https://www.baidu.com/s?wd=MYSQL {$exception->getCode()} {$exception->getMessage()}&ie=UTF-8'>[寻找解决方案]</a>",
            ];
        }

        $env = <<<EOF
DB_MODE="stand-alone"
DB_DSN="mysql:host={$this->host};port={$this->port};dbname={$this->dbname}"
DB_USER="{$this->username}"
DB_PASS="{$this->password}"
DB_PREFIX="{$this->tablePrefix}"
EOF;
        $env_file = \Yii::$app->basePath . '/.env';
        $res = file_put_contents($env_file, $env);

        if (!$res) {
            return [
                'code' => 1,
                'msg' => '文件写入失败，请检查网站目录是否有写入权限',
            ];
        }
        $db_src_file = __DIR__ . '/install.sql';
        if (!file_exists($db_src_file)) {
            return [
                'code' => 1,
                'msg' => "系统文件丢失，安装失败<br>{$db_src_file}",
            ];
        }
        $db_content = file_get_contents($db_src_file);
        $db_content = str_replace('`hjmall_', '`' . $this->tablePrefix, $db_content);
        try {
            $this->db->createCommand($db_content)->execute();
        } catch (Exception $exception) {
            return [
                'code' => 1,
                'msg' => "数据库写入失败<br>{$exception->getMessage()}",
            ];
        }
        $admin_table_name = $this->tablePrefix . 'admin';
        $admin_password = \Yii::$app->security->generatePasswordHash($this->admin_password);
        $auth_key = \Yii::$app->security->generateRandomString(32);
        $access_token = \Yii::$app->security->generateRandomString(32);
        try {
            $t = $this->db->beginTransaction();
            $sql = "INSERT INTO `{$admin_table_name}` (`id`, `username`, `password`, `auth_key`, `access_token`, `addtime`, `is_delete`, `app_max_count`, `permission`, `remark`, `expire_time`) VALUES (1,'{$this->admin_username}','{$admin_password}','{$auth_key}','{$access_token}',0,0,0,'[\"coupon\",\"share\",\"topic\",\"video\",\"copyright\"]',' ',0);";
            $res = $this->db->createCommand($sql)->execute();
            $t->commit();
            if ($res) {
                $install_lock_file = \Yii::$app->basePath . '/install.lock.php';
                $install_lock_content = 'install at ' . date('Y-m-d H:i:s') . ',host ' . \Yii::$app->request->hostInfo;
                file_put_contents($install_lock_file, '<?php exit; ?> ' . base64_encode($install_lock_content));
                return [
                    'code' => 0,
                    'msg' => 'success',
                    'sql' => $sql,
                ];
            } else {
                return [
                    'code' => 1,
                    'msg' => '数据库写入失败<br>' . $res,
                ];
            }
        } catch (Exception $exception) {
            return [
                'code' => 1,
                'msg' => "数据库写入失败<br>{$exception->getMessage()}",
            ];
        }
    }
}
