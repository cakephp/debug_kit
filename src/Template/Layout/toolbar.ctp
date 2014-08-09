<!DOCTYPE html>
<html>
	<head>
		<title>Debug Kit Toolbar</title>
		<?= $this->Html->css('DebugKit.toolbar'); ?>
	</head>
	<body>
	<?= $this->fetch('content'); ?>
	</body>
	<?= $this->Html->script('DebugKit.toolbar-app'); ?>
</html>
