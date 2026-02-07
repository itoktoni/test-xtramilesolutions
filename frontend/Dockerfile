FROM node:18-alpine

WORKDIR /app

# Build arguments for React environment variables
ARG REACT_APP_API_URL
ARG REACT_APP_PHP_API_URL

# Set environment variables for build time
ENV REACT_APP_API_URL=${REACT_APP_API_URL}
ENV REACT_APP_PHP_API_URL=${REACT_APP_PHP_API_URL}

# Copy package files
COPY package*.json ./

# Clear any cached node_modules and reinstall
RUN rm -rf node_modules package-lock.json
RUN npm install

# Copy application code
COPY . .

# Build the application
RUN npm run build

# Install serve to serve the static files
RUN npm install -g serve

# Expose port
EXPOSE 4135

# Run the application
CMD ["serve", "-s", "build", "-l", "4135"]