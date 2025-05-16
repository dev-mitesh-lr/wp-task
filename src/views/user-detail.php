<?php if ( ! empty( $user_data ) ) : ?>

    <h2>
        <?php echo esc_html( $user_data['name'] ?? '' ); ?>
        (<?php echo esc_html( $user_data['username'] ?? '' ); ?>)
    </h2>

    <?php if ( ! empty( $user_data['email'] ) ) : ?>
        <p><strong><?php esc_html_e( 'Email:', 'custom-user-table' ); ?></strong> <?php echo esc_html( $user_data['email'] ); ?></p>
    <?php endif; ?>

    <?php if ( ! empty( $user_data['phone'] ) ) : ?>
        <p><strong><?php esc_html_e( 'Phone:', 'custom-user-table' ); ?></strong> <?php echo esc_html( $user_data['phone'] ); ?></p>
    <?php endif; ?>

    <?php if ( ! empty( $user_data['website'] ) ) : ?>
        <p>
            <strong><?php esc_html_e( 'Website:', 'custom-user-table' ); ?></strong>
            <a href="<?php echo esc_url( 'http://' . ltrim( $user_data['website'], '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
                <?php echo esc_html( $user_data['website'] ); ?>
            </a>
        </p>
    <?php endif; ?>

    <?php if ( ! empty( $user_data['company']['name'] ) ) : ?>
        <p><strong><?php esc_html_e( 'Company:', 'custom-user-table' ); ?></strong> <?php echo esc_html( $user_data['company']['name'] ); ?></p>
    <?php endif; ?>

    <?php
    $address = $user_data['address'] ?? [];
    if ( ! empty( $address['street'] ) && ! empty( $address['city'] ) && ! empty( $address['zipcode'] ) ) :
        $full_address = sprintf(
            '%s, %s, %s',
            $address['street'],
            $address['city'],
            $address['zipcode']
        );
    ?>
        <p><strong><?php esc_html_e( 'Address:', 'custom-user-table' ); ?></strong> <?php echo esc_html( $full_address ); ?></p>
    <?php endif; ?>

<?php else : ?>
    <p><?php esc_html_e( 'User details not found.', 'custom-user-table' ); ?></p>
<?php endif; ?>