<?php

namespace schmunk42\giiant\generators\roles;

use yii\gii\CodeFile;
use schmunk42\giiant\helpers\SaveForm;
use yii\helpers\StringHelper;

/**
 * @link http://www.diemeisterei.de/
 *
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */
class Generator extends \yii\gii\Generator
{

    public const TYPE_USER = 'user';
    public const TYPE_COMPANY = 'company';

    public $moduleClass;
    public $roleName;
    public $roleType;
    public $roleLabel;
    public $messageCategory;

    public $migrationClassName;

    /**
     * @var string form field for selecting and loading saved gii forms
     */
    public $savedForm;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Giiant Role';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator generate advanced roles.';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return [
            'role.php',
            'migration.php'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['moduleClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
                [['moduleClass'], 'validateModuleClass'],
                [
                    [
                        'roleName',
                        'roleType',
                        'roleLabel',
                        'messageCategory'
                    ],
                    'string'
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'moduleClass' => 'Module Class',
            'messageCategory' => 'Message Category',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return [
                 'moduleClass' => 'This is the fully qualified class name of the module, e.g., <code>app\modules\admin\Module</code>.',
        ];
    }

    /**
     * Validates [[moduleClass]] to make sure it is a fully qualified class name.
     */
    public function validateModuleClass()
    {
        if (class_exists(!$this->moduleClass)) {
            $this->addError('moduleClass', 'Module class not found.');
        }
        if (empty($this->moduleClass) || substr_compare($this->moduleClass, '\\', -1, 1) === 0) {
            $this->addError('moduleClass', 'Module class name must not be empty. Please enter a fully qualified class name. e.g. "app\\modules\\admin\\Module".');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function successMessage()
    {

        $output = <<<EOD
<p>The role has been generated successfully.</p>
<p>You must start migration for adding to access controll:</p>
EOD;

        return $output;
    }

    public function formAttributes()
    {
        return [
            'moduleClass',
            'roleName',
            'roleType',
            'roleLabel',
            'messageCategory'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $roleClassName = str_replace(' ', '', ucwords(implode(' ', explode('_', $this->roleName))));
        $this->migrationClassName = 'm' . date('ymd_H0707_') . 'create_role' . $this->roleName;
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath.'/accessRights/'.$roleClassName.'UserRole.php',
            $this->render('role.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/migrations/'.$this->migrationClassName.'.php',
            $this->render('migration.php')
        );

        /*
         * create gii/[name]GiiantRole.json with actual form data
         */
        $suffix = str_replace(' ', '', $this->getName());
        $formDataFile =$this->getModulePath()
            .'/gii'
            .'/'.$this->roleName.$suffix.'.json';
        $formData = json_encode(SaveForm::getFormAttributesValues($this, $this->formAttributes()));
        $files[] = new CodeFile($formDataFile, $formData);
        return $files;
    }

    /**
     * @return bool the directory that contains the module class
     */
    public function getModulePath()
    {
        return StringHelper::dirname((new \ReflectionClass($this->moduleClass))->getFileName());
    }

    public function getRoleNamespace(): string
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')).'\accessRights';
    }

    public function getMigrationClass(): string
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')).'\accessRights';
    }

    public function getRoleTypeConstant(): string
    {
        if($this->roleType === self::TYPE_USER){
            return 'TYPE_REGULAR';
        }

        if($this->roleType === self::TYPE_COMPANY){
            return 'TYPE_COMPANY';
        }

        return '';

    }
}
