
<?php 
	include 'db_connect.php';
	$therapists= $conn->query("SELECT * FROM therapists_list ");
	while($row = $therapists->fetch_assoc()){
		$doc_arr[$row['id']] = $row;
	}
	$customer= $conn->query("SELECT * FROM users where type = 3 ");
	while($row = $customer->fetch_assoc()){
		$p_arr[$row['id']] = $row;
	}
?>
<div class="container-fluid">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<button class="btn-primary btn btn-sm" type="button" id="new_appointment"><i class="fa fa-plus"></i> New Appointment</button>
				<br>
				<table class="table table-bordered">
					<thead>
						<tr>
						<th>Schedule</th>
						<th>Therapists</th>
						<th>Customer</th>
						<th>Status</th>
						<th>Reservation Fee</th>
						<th>Action</th>
						
					</tr>
					</thead>
					<?php 
					$where = '';
					if($_SESSION['login_type'] == 2)
						$where = " where therapists_id = ".$_SESSION['login_therapists_id'];
					$qry = $conn->query("SELECT * FROM appointment_list ".$where." order by id desc ");
					while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<td><?php echo date("l M d, Y h:i A",strtotime($row['schedule'])) ?></td>
						<td><?php echo "".$doc_arr[$row['therapists_id']]['name'].', '.$doc_arr[$row['therapists_id']]['name'] ?></td>
						<td><?php echo $p_arr[$row['customer_id']]['name'] ?></td>
						<td>
							<?php if($row['status'] == 0): ?>
								<span class="badge badge-warning">Pending Request</span>
							<?php endif ?>
							<?php if($row['status'] == 1): ?>
								<span class="badge badge-primary">Confirmed</span>
							<?php endif ?>
							<?php if($row['status'] == 2): ?>
								<span class="badge badge-info">Rescheduled</span>
							<?php endif ?>
							<?php if($row['status'] == 3): ?>
								<span class="badge badge-info">Done</span>
							<?php endif ?>
							<td><?php echo "<img src='" . $row['image_url'] . "' alt='Appointment Image'>";?></td>
						<td class="text-center">
							<button  class="btn btn-primary btn-sm update_app" type="button" data-id="<?php echo $row['id'] ?>">Update</button>
							<button  class="btn btn-danger btn-sm delete_app" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
						</td>
					</tr>
				<?php endwhile; ?>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	$('.update_app').click(function(){
		uni_modal("Edit Appintment","set_appointment.php?id="+$(this).attr('data-id'),"mid-large")
	})
	$('#new_appointment').click(function(){
		uni_modal("Add Appintment","set_appointment.php","mid-large")
	})
	$('.delete_app').click(function(){
		_conf("Are you sure to delete this appointment?","delete_app",[$(this).attr('data-id')])
	})
	function delete_app($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_appointment',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>