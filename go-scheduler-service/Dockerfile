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

# Create state.json from example if not exists
RUN cp state.json.example state.json 2>/dev/null || true

# Create users directory
RUN mkdir -p users && chmod 777 users

# Run the application
CMD ["./main"]