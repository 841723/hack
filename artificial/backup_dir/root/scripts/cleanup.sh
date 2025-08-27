#!/bin/bash
rm -r /dev/shm/*
shopt -s extglob
rm -r /tmp/!(systemd*)
rm -r /home/*/!(.bash_logout|.bashrc|user.txt|.ssh|.profile|.bash_history|.python_history|.sqlite_history|app)
rm -r /home/app/app/!(app.py|instance|models|__pycache__|static|templates)
rm -r /home/app/app/instance/!(users.db)
rm -r /home/app/app/static/!(css|js|requirements.txt|Dockerfile)
rm -r /home/app/app/static/css/!(styles.css)
rm -r /home/app/app/static/js/!(scripts.js)
rm -r /root/.ssh/!(id_rsa|authorized_keys)
rm -r /root/!(root.txt|scripts)
rm -r /home/app/app/templates/!(dashboard.html|index.html|login.html|register.html|run_model.html|sales_statistics.html)
shopt -u extglob
rm -r /home/app/app/models/*
rm -r /opt/backrest/.config/backrest/*
cp /root/scripts/config.json /opt/backrest/.config/backrest/config.json
chmod 600 /opt/backrest/.config/backrest/config.json
truncate -s 0 /opt/backrest/processlogs/backrest.log
sqlite3 /opt/backrest/oplog.sqlite 'delete from operations; delete from operation_groups'
/usr/sbin/service backrest restart
