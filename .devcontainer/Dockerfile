ARG VARIANT=7.4
FROM mcr.microsoft.com/vscode/devcontainers/php:${VARIANT}

# Install MariaDB client.
RUN apt-get update && apt-get -y upgrade && export DEBIAN_FRONTEND=noninteractive \
   && apt-get install -y mariadb-client \
   && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Add vscode user.
ARG USER_UID=1000
ARG USER_GID=$USER_UID
RUN if [ "$USER_GID" != "1000" ] || [ "$USER_UID" != "1000" ]; then \
        groupmod --gid $USER_GID vscode \
        && usermod --uid $USER_UID --gid $USER_GID vscode \
        && chown -R $USER_UID:$USER_GID /home/vscode \
        && chown -R $USER_UID:root /usr/local/share/nvm; \
    fi

# Install node lts.
ARG INSTALL_NODE="true"
ARG NODE_VERSION="lts/*"
RUN if [ "${INSTALL_NODE}" = "true" ]; then su vscode -c "source /usr/local/share/nvm/nvm.sh && nvm install ${NODE_VERSION} 2>&1"; fi

# Install php extentsions.
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && install-php-extensions yaml gd pdo_mysql zip

# Install drush launcher.
RUN wget -O drush.phar https://github.com/drush-ops/drush-launcher/releases/latest/download/drush.phar \
  && chmod +x drush.phar && mv drush.phar /usr/local/bin/drush

# Install rclone.
# RUN curl https://rclone.org/install.sh | bash

# [Optional] Uncomment this section to install additional OS packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#     && apt-get -y install --no-install-recommends nano

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1
