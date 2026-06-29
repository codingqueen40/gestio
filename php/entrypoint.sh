#!/bin/sh
set -e

# Configure msmtp as sendmail relay si les variables SMTP sont fournies.
# PHP mail() utilise alors msmtp-mta (qui remplace /usr/sbin/sendmail).
if [ -n "$SMTP_HOST" ] && [ -n "$SMTP_USER" ] && [ -n "$SMTP_PASSWORD" ]; then
    cat > /etc/msmtprc << MSMTPCONF
defaults
auth           on
tls            on
tls_trust_file /etc/ssl/certs/ca-certificates.crt

account        default
host           $SMTP_HOST
port           ${SMTP_PORT:-587}
from           ${SMTP_FROM:-noreply@gestio.local}
user           $SMTP_USER
password       $SMTP_PASSWORD
MSMTPCONF
    chmod 600 /etc/msmtprc
fi

exec apache2-foreground
