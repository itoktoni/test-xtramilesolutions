FROM golang:1.21-alpine

WORKDIR /app

# Copy go mod files
COPY go.mod go.sum ./

# Download dependencies
RUN go mod download

# Copy source code
COPY . .

# Build the application
RUN CGO_ENABLED=0 GOOS=linux go build -o main .

# Install ca-certificates
RUN apk --no-cache add ca-certificates

# Create directories
RUN mkdir -p storage data && chmod 777 storage data

# Create entrypoint script - copies state.json.example to data/state.json ONLY if data/state.json doesn't exist
RUN printf '#!/bin/sh\nif [ ! -f /app/data/state.json ]; then cp /app/state.json.example /app/data/state.json; fi\nexec /app/main\n' > /app/entrypoint.sh && chmod +x /app/entrypoint.sh

# Run the application - environment variables are passed at runtime via docker-compose
ENTRYPOINT ["/app/entrypoint.sh"]
CMD ["./main"]