<?php $__env->startSection('content'); ?>
        <div class="col-md">
            <img class="img-thumbnail" src="<?php echo e($picture); ?>"/>
            <h2>Thanks <?php echo e($name); ?>! 👏</h2>
        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('welcome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jordan/Developer/assignment2/resources/views/thanks.blade.php ENDPATH**/ ?>