# Generate the HTML output.
FROM markstory/cakephp-docs-builder as builder

RUN pip install git+https://github.com/sphinx-contrib/video.git@master

# Copy entire repo in with .git so we can build all versions in one image.
COPY docs /data/src

# Make docs
RUN cd /data/docs-builder \
  && make website LANGS="en fr ja pt" SOURCE=/data/src DEST=/data/website \
  # Move media files into the output directory so video elements work.
  && mkdir -p /data/website/html/_static \
  && cp /data/src/static/* /data/website/html/_static/

# Build a small nginx container with just the static site in it.
FROM nginx:1.15-alpine

COPY --from=builder /data/website /data/website
COPY --from=builder /data/docs-builder/nginx.conf /etc/nginx/conf.d/default.conf

# Move files into final location
RUN cp -R /data/website/html/* /usr/share/nginx/html \
  && rm -rf /data/website/
