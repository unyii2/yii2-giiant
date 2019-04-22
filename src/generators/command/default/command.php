<?php
/**
 * @var  yii\web\View $this
 * @var schmunk42\giiant\generators\command\Generator $generator
 */

echo "<?php\n";
?>

namespace <?= $generator->getCommandNamespace() ?>;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;

class <?=$generator->getCommandClassName()?> extends Controller
{

    /**
     * default action
     * @return int
     */
    public function actionIndex()
    {
        $this->out('Started ' . date('Ymd His') );
        $connection = $this->getConnection();

        $this->out('Finished ' . date('Ymd His') );
        return ExitCode::OK;
    }

    /**
     * @return Connection
     */
    private function getConnection(): Connection
    {
        return \Yii::$app->getDb();
    }

    /**
     * output to terminal line
     * @param string $string output string
     * @param int $settings
     */
    public function out(string $string,int $settings = 0): void
    {
        $this->stdout($string . PHP_EOL, $settings);
    }

}

