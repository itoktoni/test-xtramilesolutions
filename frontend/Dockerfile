FROM node:18-alpine

WORKDIR /app

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