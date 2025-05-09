FROM node:20

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy application files
COPY . .


# Expose port
EXPOSE 5173

# Start Vue development server
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]