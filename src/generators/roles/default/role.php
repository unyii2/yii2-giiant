<?php
/**
 * @var  yii\web\View $this
 * @var schmunk42\giiant\generators\roles\Generator $generator
 */

echo "<?php\n";
?>

namespace <?= $generator->getRoleNamespace() ?>;


use CompanyRights\components\UserRoleInterface;
use yii2d3\d3persons\accessRights\CompanyOwnerUserRole;

class <?=$generator->roleName?>UserRole implements UserRoleInterface
{

    public const NAME = '<?=$generator->roleName?>';

    /**
    * @inheritdoc
    */
    public function getType(): string
    {
        return self::<?=$generator->getRoleTypeConstant()?>;
    }

    /**
    * @inheritdoc
    */
    public function getLabel(): string
    {
        return \Yii::t('<?=$generator->messageCategory?>', '<?=$generator->roleLabel?>');

    }

    /**
    * @inheritdoc
    */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
    * @inheritdoc
    */
    public function getAssigments(): array
    {
        return [];
    }

    /**
    * @inheritdoc
    */
    public function canAssign(): bool
    {
        return \Yii::$app->user->can(CompanyOwnerUserRole::NAME);
    }

    /**
    * @inheritdoc
    */
    public function canView(): bool
    {
        //return \Yii::$app->user->can(SystemAdminUserRole::NAME);
        return \Yii::$app->user->can(CompanyOwnerUserRole::NAME);
    }

    /**
    * @inheritdoc
    */
    public function canRevoke(): bool
    {
        return \Yii::$app->user->can(CompanyOwnerUserRole::NAME);
    }

}

