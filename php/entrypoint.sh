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
# "-" = stdout du conteneur, donc visible dans les logs Coolify.
# Sans ça, un echec SMTP (auth refusee, relais injoignable) est totalement
# silencieux : mail() renvoie false et personne ne sait pourquoi.
logfile        -

account        default
host           $SMTP_HOST
port           ${SMTP_PORT:-587}
from           ${SMTP_FROM:-noreply@gestio.local}
user           $SMTP_USER
password       $SMTP_PASSWORD
MSMTPCONF
    # Ce script tourne en root, mais PHP mail() est execute par les workers
    # Apache, qui tournent en www-data. Sans ce chown, www-data ne peut pas
    # lire un fichier 0600 appartenant a root : msmtp echoue et AUCUN mail ne
    # part, sans le moindre message d'erreur. Piege attrape au ticket #51.
    chown www-data:www-data /etc/msmtprc
    # 0600 conserve : msmtp refuse un fichier de config contenant un mot de
    # passe s'il est lisible par le groupe ou par les autres.
    chmod 600 /etc/msmtprc
fi

exec apache2-foreground
