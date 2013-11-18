<?php
								$result=$vo['total_price']/$vo['target_price']*100;
								if(ceil($result)==$result)
								echo $result.'%';
								else
								echo sprintf("%01.2f ",$vo['total_price']/$vo['target_price']*100).'%'
							?>
