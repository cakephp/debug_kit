<?php
/* SVN FILE: $Id$ */
/**
 * Log Panel Element
 *
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2006-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       cake
 * @subpackage    cake.debug_kit.views.elements
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author        Yasushi Ichikawa
 */
?>
<h2><?php __('Sql Explain Results')?></h2>
<?php if (!empty($content)) : ?>


	<?php foreach( $content as $configName => $explain_results): ?>
		<?php
			$driver = $explain_results['sqlexplain_driver'];
			unset($explain_results['sqlexplain_driver']);
		?>

		<div class="cake-sql-log">

		<?php if( $driver === 'mysql' || $driver === 'postgres' ): ?>


			<?php
				$headers = array_keys($explain_results[0]);
				$row = array();
				
				foreach( $explain_results as $rownum => $value ){

					foreach( $value as $title => $linevalue ){
						if( is_array($linevalue) ){
							$linevalue_li = "<ul>";
							foreach( $linevalue as $num => $arr_val ){
								$linevalue_li  .= "<li>";
								$linevalue_li  .= $arr_val;
								$linevalue_li  .= "</li>";
							}
							$linevalue_li .= "</ul>";
							$linevalue = $linevalue_li;
						}
						$row[$rownum][] = $linevalue;
					}

				}
				
				echo $configName;
				echo $toolbar->table($row, $headers, array('title' => 'SQL Explain Results'));
			?>




		<?php else: ?>
			<p><?php __('support only MySQL and PostgreSQL.'); ?></p>

		<?php endif; ?>

		</div>
	<?php endforeach; ?>


<?php else: ?>
	<p class="warning"><?php __('No active database connections'); ?></p>
<?php endif; ?>