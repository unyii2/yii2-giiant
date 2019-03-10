<?php
/**
 * @var  yii\web\View $this
 * @var schmunk42\giiant\generators\roles\Generator $generator
 */

echo "<?php\n";
?>



use yii\db\Migration;
use <?= $generator->getRoleNamespace() ?>\<?=$generator->roleName?>UserRole;

class <?=$generator->migrationClassName?>  extends Migration {

    public function up() {

        $auth = \Yii::$app->authManager;
        $role = $auth->createRole(<?=$generator->roleName?>UserRole::NAME);
        $auth->add($role);

    }

    public function down() {
        $auth = Yii::$app->authManager;
        $role = $auth->createRole(<?=$generator->roleName?>UserRole::NAME);
        $auth->remove($role);
    }
}
