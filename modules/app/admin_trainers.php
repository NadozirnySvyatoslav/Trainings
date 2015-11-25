<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../trainer.php';
include LC_PATH . '/common.php';
include LC_PATH . '/trainers.php';
class AdminTrainersPage extends AuthorizedPage {
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$trainer = new Trainer ();
		$page = $_GET ['page'];
		$offset = intval ( $page ) * ITEMS_IN_PAGE;
		if (isset ( $_GET ['search'] )) {
			unset ( $_GET ['search'] );
			unset ( $_GET ['page'] );
			$search = $_GET;
		} else {
			$search = null;
		}
		$cnt = $trainer->getCount ( $search );
		
		if (sizeof ( $_GET ) > 0) {
			foreach ( $_GET as $key => $val ) {
				$values [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
		}
		
		if ($cnt > ITEMS_IN_PAGE)
			$pagination = $this->addPaginator ( $cnt, $page );
		
		echo <<< EOF
<h2  class="page-header">{$translator->Trainers_header} <span class="badge">$cnt</span></h2>
    <div class="row">
	<div class="btn-group">
	<a href="/admin_trainer/new" class="btn btn-sm btn-default">
	    <span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-user"></span> {$translator->Add}</a>
	<button class="btn btn-sm btn-default"  data-toggle="collapse" data-target="#filter">
	    <span class="glyphicon glyphicon-filter"></span> {$translator->Filter}
	</button>
	</div>
    </div>
    <div class="row">
        <div class="col-md-5 collapse" id="filter">
	    <form role="form" class="form-horizontal" action="?" method="get">
		<div class="form-group">
		    <div class="form-group">
		    <label for="name" class="control-label col-sm-3">{$translator->Name}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="name" name="name" value="{$values[name]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="email" class="control-label col-sm-3">{$translator->Email}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="email" name="email" value="{$values[email]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="data" class="control-label col-sm-3">{$translator->Data}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="data" name="data" value="{$values[data]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <div class="col-sm-offset-3 col-md-10">
		    <input class="sr-only" name="search" value="1">
		    <button type="submit" class="btn btn-sm btn-default">{$translator->Apply}</button>
		    </div>
		    </div>
		</div>
	    </form>
	</div>
    </div>
{$pagination}
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{$translator->Name}</th>
                  <th>{$translator->Email}</th>
                  <th>{$translator->Data}</th>
                </tr>
              </thead>
              <tbody>

EOF;
		
		$enum = $trainer->enumerate ( $search, $offset, ITEMS_IN_PAGE );
		if (is_object ( $enum )) {
			$i = 1 + $offset;
			foreach ( $enum as $key => $data ) {
				if (strlen ( $data ['data'] ) > 50)
					$data ['data'] = '<small>' . $data ['data'] . '</small>';
				else if (strlen ( $data ['data'] ) > 200)
					$data ['data'] = '<small>' . substr ( $data ['data'], 0, 200 ) . '...</small>';
				echo "                <tr>
                  <td>{$i}</td>
                  <td>{$data[name]}</td>
                  <td>{$data[email]}</td>
                  <td>{$data[data]}</td>
                  <td><a href=\"/admin_trainer/edit/{$data[id]}\" title=\"{$translator->edit}\"><span class=\"glyphicon glyphicon-user\"></span></a></td>
                  <td><a href=\"/admin_trainer/delete/{$data[id]}\" title=\"{$translator->del}\" onClick=\"return confirm('{$translator->AreYouSure}');\"><span class=\"glyphicon glyphicon-trash\"></span></a></td>
                </tr>" . NL;
				$i ++;
			}
		}
		
		echo <<< EOF
	    </table>
	</div>

EOF;
	}
	function defaultRole() {
		$this->role = User::ADMIN;
	}
}
