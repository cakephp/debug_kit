# Generate the HTML output.
FROM markstory/cakephp-docs-builder as builder

RUN pip install git+https://github.com/sphinx-contrib/video.git@master

COPY docs /data/docs

RUN cd /data/docs-builder && \
  # In the future repeat website for each version
  make website LANGS="en fr ja pt" SOURCE=/data/docs DEST=/data/website/3.x

# Move media files into the output directory so video elements work.
RUN mkdir -p /data/website/3.x/html/_static \
 && cp /data/docs/static/* /data/website/3.x/html/_static/

# Build a small nginx container with just the static site in it.
FROM nginx:1.15-alpine

COPY --from=builder /data/website /data/website
COPY --from=builder /data/docs-builder/nginx.conf /etc/nginx/conf.d/default.conf

# Move each version into place
RUN mv /data/website/3.x/html/ /usr/share/nginx/html/3.x
