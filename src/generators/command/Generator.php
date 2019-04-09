<?php

namespace schmunk42\giiant\generators\command;

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

    public $moduleClass;
    public $commandName;

    /**
     * @var string form field for selecting and loading saved gii forms
     */
    public $savedForm;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Giiant Command';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator generate command controller.';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return [
            'command.php',
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
                        'commandName',
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
            'commandName' => 'Command Name',
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
        $command = strtolower(str_replace('\\','-',$this->getCommandNamespace())
            . '-'
            . implode('-', explode('_', $this->commandName)));
        $command = str_replace('-commands-','-',$command);
        $output = '<p>The command has been generated successfully.</p>
                   <p>Please add follow code in console conig under "controllerMap".</p>
                   <br/><code>\''.$command.'\' => \''.$this->getCommandNamespace(). '\\' . $this->getCommandClassName() . '\'</code>';

        return $output;
    }

    public function formAttributes()
    {
        return [
            'moduleClass',
            'commandName',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath.'/commands/'.$this->getCommandClassName().'.php',
            $this->render('command.php')
        );

        /*
         * create gii/[name]GiiantCommand.json with actual form data
         */
        $suffix = str_replace(' ', '', $this->getName());

        $formDataFile = $this->getModulePath()
            .'/gii'
            .'/'.$this->commandName.$suffix.'.json';
        $formData = json_encode(SaveForm::getFormAttributesValues($this, $this->formAttributes()));
        $files[] = new CodeFile($formDataFile, $formData);
        return $files;
    }

    /**
     * @return string the directory that contains the module class
     * @throws \ReflectionException
     */
    public function getModulePath(): string
    {
        return StringHelper::dirname((new \ReflectionClass($this->moduleClass))->getFileName());
    }

    public function getCommandNamespace(): string
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')).'\commands';
    }

    public function getCommandClassName(): string
    {
        return str_replace(' ', '', ucwords(implode(' ', explode('_', $this->commandName)))).'Command';
    }


}
