<div class="header">
	<div class="top_l">
		<?php if ($this->session->userdata('user_id')):?>
		<h2>欢迎您， <?php echo $this->session->userdata('user_name')?><a href="/user/logout">退出登陆</a></h2>
		<?php else:?>
		<h2>欢迎您，请<a href="/user/login" >登陆</a>/<a href="/user/register" >注册</a></h2>
		<?php endif;?>
	</div>
	<div class="top_r">
		<ul>
			<li><a href="/index.php">首页</a></li>
			<li><a href="/user">个人中心</a></li>
		</ul>
	</div>
</div>