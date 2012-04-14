<?php
if (!empty($this->request->params['isAjax'])) {
	return;
}
echo $this->Tidy->report($this->output);