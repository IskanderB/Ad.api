@setup
  $user = "axurt";

  $timezone = "Europe/Moscow";

  $path = "/var/www/ad.tutorib.ru";

  $current = $path . "/current";

  $repo = "git@github.com:IskanderB/Ad.api.git";

  $branch = "master";

  $chmods = [
  'storage/logs'
  ];

  $date = new DateTime('now', new DateTimeZone($timezone));
  $release = $path . '/releases/' . $date->format('YmdTis');
@endsetup

@servers(['production' => $user . "@45.80.71.86"])

@task('clone', ['on' => $on])
  mkdir -p {{ $release }}
  git clone --depth 1 -b {{ $branch }} {{ $repo }} {{ $release }}
  echo "#1 - Repository has been cloned"
@endtask

@task('chmod', ['on' => $on])
  chgrp -R www-data {{$release}};
  chmod -R ug+rwx {{$release}};

  @foreach($chmods as $file)
    chmod -R 775 {{$release}}/{{$file}}
    chown -R {{$user}}:www-data {{$release}}/{{$file}}

    echo "Permissions have been set for {{$file}}"
  @endforeach

  echo "Permissions have been set"
@endtask

@task("update_symlinks")
  ln -nfs {{$release}} {{$current}};
  chgrp -h www-data {{$current}};

  echo "#5 - Symlink has been set"
@endtask

@macro('deploy', ['on' => 'production'])
  clone
  chmod
  update_symlinks
@endmacro
