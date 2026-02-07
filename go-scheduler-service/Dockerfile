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

# Create users directory
RUN mkdir -p users && chmod 777 users

# Create data directory with state.json
RUN mkdir -p data && cp state.json.example data/state.json

# Create entrypoint script
RUN printf '#!/bin/sh\nif [ ! -f /app/data/state.json ]; then cp /app/state.json.example /app/data/state.json; fi\nexec /app/main\n' > /app/entrypoint.sh && chmod +x /app/entrypoint.sh

# Run the application
ENTRYPOINT ["/app/entrypoint.sh"]
CMD ["./main"]