# Generate the HTML output.
FROM markstory/cakephp-docs-builder as builder

RUN pip install git+https://github.com/sphinx-contrib/video.git@master

# Copy entire repo in with .git so we can build all versions in one image.
COPY . /data/src

# Make static directories

# Make 3.x docs
RUN cd /data/docs-builder \
  && make website LANGS="en fr ja pt" SOURCE=/data/src/docs DEST=/data/website/3.x \
  # Move media files into the output directory so video elements work.
  && mkdir -p /data/website/3.x/html/_static \
  && cp /data/src/docs/static/* /data/website/3.x/html/_static/ \
  # Make 4.x docs
  && cd /data/src && git checkout 4.x \
  && cd /data/docs-builder \
  && make website LANGS="en fr ja pt" SOURCE=/data/src/docs DEST=/data/website/4.x \
  && mkdir -p /data/website/4.x/html/_static \
  && cp /data/src/docs/static/* /data/website/4.x/html/_static/

# Build a small nginx container with just the static site in it.
FROM nginx:1.15-alpine

COPY --from=builder /data/website /data/website
COPY --from=builder /data/docs-builder/nginx.conf /etc/nginx/conf.d/default.conf

# Move each version into place
RUN mv /data/website/3.x/html/ /usr/share/nginx/html/3.x && \
  mv /data/website/4.x/html/ /usr/share/nginx/html/4.x
