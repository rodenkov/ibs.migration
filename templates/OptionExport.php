<?php

/**
 * @var $version
 * @var $description
 * @var $items
 * @var $extendUse
 * @var $extendClass
 * @var $moduleVersion
 * @formatter:off
 */

?><?php echo "<?php\n" ?>

namespace IBS\Migration;

<?php echo $extendUse ?>

class <?php echo $version ?> extends <?php echo $extendClass ?>

{
    protected $description = "<?php echo $description ?>";

    protected $moduleVersion = "<?php echo $moduleVersion ?>";

    public function up()
    {
        $helper = $this->getHelperManager();
<?php foreach ($items as $item):?>
        $helper->Option()->saveOption(<?php echo var_export($item, 1) ?>);
<?php endforeach; ?>
    }

    public function down()
    {
        //your code ...
    }
}
