<p class="notice">Enter one IP address per line.</p>

<!-- diplay the value from get options and they can also add new -->

<textarea name="allowed_ips" rows="10" cols="50" class="large-text">
    <?php echo esc_textarea(implode("\n", $args["allowed_ips"])); ?>

</textarea>